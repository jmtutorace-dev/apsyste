<?php
include 'includes/session.php';

/*
 * Biometric attendance importer.
 *
 * Reads a CSV exported from the biometric device. Expected columns (matched by
 * header name, order-independent): Staff Code, Date, Time1..Time12 (and an
 * ignored Name / Department / Week / Remark).
 *
 * - Employees are matched on employees.biometric_id (the device "Staff Code").
 * - Per day: earliest punch = Time In, latest punch = Time Out.
 * - Days with no punches are skipped.
 * - Existing employee+date records are overwritten.
 */

if(!isset($_POST['import'])){
    $_SESSION['error'] = 'No import submitted.';
    header('location: attendance.php');
    exit();
}

// ---------------------------------------------------------------
// VALIDATE UPLOAD
// ---------------------------------------------------------------

if(!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK){
    $_SESSION['error'] = 'Please choose a CSV file to upload.';
    header('location: attendance.php');
    exit();
}

$ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
if($ext !== 'csv'){
    $_SESSION['error'] = 'Unsupported file type ".'.$ext.'". Save the Excel file as CSV first, then upload.';
    header('location: attendance.php');
    exit();
}

// ---------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------

// Normalize a staff/biometric code so "00000001" and "1" match.
function norm_code($s){
    $s = trim($s);
    if($s === '') return '';
    $t = ltrim($s, '0');
    return $t === '' ? '0' : $t;
}

// Parse various date layouts into Y-m-d (prefers US M/D/Y, the device's format).
function parse_date($s){
    $s = trim($s);
    if($s === '') return null;

    $formats = ['n/j/Y', 'm/d/Y', 'Y-m-d', 'Y/m/d', 'n/j/y', 'm/d/y', 'd-M-Y', 'j-M-Y'];

    foreach($formats as $f){
        $d = DateTime::createFromFormat($f, $s);
        $e = DateTime::getLastErrors();
        // PHP 8.2 returns false from getLastErrors() when there were none.
        if($d !== false && (!$e || ($e['warning_count'] == 0 && $e['error_count'] == 0))){
            return $d->format('Y-m-d');
        }
    }

    $ts = strtotime($s);
    return $ts ? date('Y-m-d', $ts) : null;
}

// Parse a punch time into H:i:s (handles 08:23, 8:23, 08:23:00, 8:23 AM).
function parse_time($s){
    $s = trim($s);
    if($s === '') return null;
    $ts = strtotime($s);
    if($ts === false) return null;
    return date('H:i:s', $ts);
}

// ---------------------------------------------------------------
// BUILD EMPLOYEE LOOKUP (biometric_id -> id + schedule time_in)
// ---------------------------------------------------------------

$exact = array();   // trimmed biometric_id  => ['id'=>, 'sched_in'=>]
$loose = array();   // normalized code       => ['id'=>, 'sched_in'=>]

$emp_sql = "
    SELECT e.id, e.biometric_id, s.time_in AS sched_in
    FROM employees e
    LEFT JOIN schedules s ON s.id = e.schedule_id
    WHERE e.biometric_id IS NOT NULL AND e.biometric_id <> ''
";

$emp_res = $conn->query($emp_sql);

if($emp_res){
    while($e = $emp_res->fetch_assoc()){
        $info = array('id' => $e['id'], 'sched_in' => $e['sched_in']);
        $bio = trim($e['biometric_id']);
        $exact[$bio] = $info;
        $loose[norm_code($bio)] = $info;
    }
}

if(count($exact) === 0){
    $_SESSION['error'] = 'No employees have a Biometric ID yet. Open the Employees page and set each employee\'s Biometric ID (their device Staff Code) before importing.';
    header('location: attendance.php');
    exit();
}

// ---------------------------------------------------------------
// OPEN CSV + DETECT DELIMITER
// ---------------------------------------------------------------

$handle = fopen($_FILES['csv_file']['tmp_name'], 'r');

if($handle === false){
    $_SESSION['error'] = 'Could not read the uploaded file.';
    header('location: attendance.php');
    exit();
}

$firstLine = fgets($handle);
$firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine); // strip UTF-8 BOM

$counts = array(
    ','  => substr_count($firstLine, ','),
    ';'  => substr_count($firstLine, ';'),
    "\t" => substr_count($firstLine, "\t"),
);
arsort($counts);
$delim = array_key_first($counts);

rewind($handle);

// ---------------------------------------------------------------
// LOCATE HEADER + MAP COLUMNS
// ---------------------------------------------------------------

$staffIdx = null;
$dateIdx  = null;
$timeIdx  = array();
$found_header = false;

while(($cells = fgetcsv($handle, 0, $delim)) !== false){

    // Clean each cell (strip BOM + whitespace) and lowercase for matching.
    $norm = array();
    foreach($cells as $i => $c){
        $c = preg_replace('/^\xEF\xBB\xBF/', '', $c);
        $norm[$i] = strtolower(trim($c));
    }

    $hasStaff = false;
    $hasDate  = false;
    foreach($norm as $v){
        if($v === 'staff code' || (strpos($v, 'staff') !== false && strpos($v, 'code') !== false)) $hasStaff = true;
        if($v === 'date') $hasDate = true;
    }

    if($hasStaff && $hasDate){
        foreach($norm as $i => $v){
            if($staffIdx === null && ($v === 'staff code' || (strpos($v, 'staff') !== false && strpos($v, 'code') !== false))){
                $staffIdx = $i;
            }
            elseif($dateIdx === null && $v === 'date'){
                $dateIdx = $i;
            }
            elseif(preg_match('/^time\s*\d+$/', $v)){
                $timeIdx[] = $i;
            }
        }
        $found_header = true;
        break; // data rows follow
    }
}

if(!$found_header || $staffIdx === null || $dateIdx === null || count($timeIdx) === 0){
    fclose($handle);
    $_SESSION['error'] = 'Could not find the expected columns (Staff Code, Date, Time1..Time12) in the CSV header. Make sure you uploaded the biometric export with its header row.';
    header('location: attendance.php');
    exit();
}

// ---------------------------------------------------------------
// PREPARE UPSERT STATEMENTS
// ---------------------------------------------------------------

$find = $conn->prepare("SELECT id FROM attendance WHERE employee_id = ? AND date = ? LIMIT 1");
$ins  = $conn->prepare("INSERT INTO attendance (employee_id, date, time_in, status, time_out, num_hr) VALUES (?, ?, ?, ?, ?, ?)");
$upd  = $conn->prepare("UPDATE attendance SET time_in = ?, status = ?, time_out = ?, num_hr = ? WHERE id = ?");

// ---------------------------------------------------------------
// PROCESS DATA ROWS
// ---------------------------------------------------------------

$inserted   = 0;
$updated    = 0;
$skipped    = 0;   // days with no punch
$bad_date   = 0;
$unmatched  = array(); // staff code => true

while(($cells = fgetcsv($handle, 0, $delim)) !== false){

    // skip completely blank lines
    if(count($cells) === 1 && trim((string)$cells[0]) === '') continue;

    $code = isset($cells[$staffIdx]) ? trim($cells[$staffIdx]) : '';
    if($code === '') continue;

    // match employee
    if(isset($exact[$code])){
        $emp = $exact[$code];
    }
    elseif(isset($loose[norm_code($code)])){
        $emp = $loose[norm_code($code)];
    }
    else{
        $unmatched[$code] = true;
        continue;
    }

    // date
    $date = isset($cells[$dateIdx]) ? parse_date($cells[$dateIdx]) : null;
    if($date === null){
        $bad_date++;
        continue;
    }

    // collect punches
    $times = array();
    foreach($timeIdx as $i){
        if(!isset($cells[$i])) continue;
        $t = parse_time($cells[$i]);
        if($t !== null) $times[] = $t;
    }

    if(count($times) === 0){
        $skipped++; // absent / no punch
        continue;
    }

    sort($times);
    $time_in  = $times[0];
    $time_out = count($times) > 1 ? $times[count($times) - 1] : '00:00:00';

    // worked hours (minus a 1h break when the span is more than 4h)
    $num_hr = 0;
    if($time_out !== '00:00:00'){
        $din  = strtotime($date.' '.$time_in);
        $dout = strtotime($date.' '.$time_out);
        $diff = ($dout - $din) / 3600;
        if($diff < 0) $diff = 0;
        if($diff > 4) $diff -= 1;
        $num_hr = round($diff, 2);
    }

    // status: 1 = on time, 0 = late (vs schedule time_in)
    $status = 1;
    if(!empty($emp['sched_in'])){
        $status = (strtotime($time_in) > strtotime($emp['sched_in'])) ? 0 : 1;
    }

    $emp_id = $emp['id'];

    // upsert
    $find->bind_param('is', $emp_id, $date);
    $find->execute();
    $find->store_result();

    if($find->num_rows > 0){
        $find->bind_result($att_id);
        $find->fetch();
        $upd->bind_param('sisdi', $time_in, $status, $time_out, $num_hr, $att_id);
        $upd->execute();
        $updated++;
    }
    else{
        $ins->bind_param('issisd', $emp_id, $date, $time_in, $status, $time_out, $num_hr);
        $ins->execute();
        $inserted++;
    }

    $find->free_result();
}

fclose($handle);

// ---------------------------------------------------------------
// SUMMARY
// ---------------------------------------------------------------

if($inserted === 0 && $updated === 0){
    $msg = 'No attendance was imported.';
    if(count($unmatched) > 0){
        $msg .= ' None of the Staff Codes matched an employee Biometric ID. Set the Biometric ID on the Employees page. Unmatched: '.implode(', ', array_slice(array_keys($unmatched), 0, 15)).'.';
    }
    $_SESSION['error'] = $msg;
}
else{
    $msg = "Import complete: $inserted added, $updated updated.";
    if($skipped > 0)  $msg .= " $skipped day(s) skipped (no punches).";
    if($bad_date > 0) $msg .= " $bad_date row(s) skipped (unreadable date).";
    if(count($unmatched) > 0){
        $codes = array_slice(array_keys($unmatched), 0, 15);
        $msg .= ' '.count($unmatched).' unmatched Staff Code(s) with no employee Biometric ID: '.implode(', ', $codes).(count($unmatched) > 15 ? ', …' : '').'.';
    }
    $_SESSION['success'] = $msg;
}

header('location: attendance.php');
exit();
?>

<?php
include 'includes/session.php';
include 'includes/thirteenth_month_compute.php';
include 'includes/settings.php';

if(!isset($_GET['id']) || !isset($_GET['type'])){
    $_SESSION['error'] = 'Invalid request';
    header('location: thirteenth_month.php');
    exit();
}

$empid = intval($_GET['id']);
$type  = trim($_GET['type']);

// Year to release for (defaults to current year)
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

if($type != 'Midyear' && $type != 'Year End'){
    $_SESSION['error'] = 'Invalid release type';
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

/*
|--------------------------------------------------------------------------
| COMPUTE 13TH MONTH (shared, attendance-based)
|--------------------------------------------------------------------------
*/

$tm = compute_thirteenth_month($conn, $empid, $year);

if(!$tm['found']){
    $_SESSION['error'] = 'Employee not found';
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

$thirteenthMonth = $tm['thirteenth_month'];
$totalReleased   = $tm['released'];

if(!$tm['entitled']){
    $_SESSION['error'] =
        'This employee is not yet entitled to 13th month pay for '.$year.
        ' (needs at least one month of service within the year).';
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

if($thirteenthMonth <= 0){
    $_SESSION['error'] =
        'No 13th month pay to release for '.$year.
        ' (no basic salary earned / no attendance recorded).';
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

/*
|--------------------------------------------------------------------------
| SETTINGS  (split mode / midyear percentage)
|--------------------------------------------------------------------------
*/

$cfg            = get_payroll_settings($conn);
$mode           = ($cfg['thirteenth_mode'] === 'split') ? 'split' : 'full';
$midyearPercent = max(0, min(100, (float) $cfg['midyear_percentage']));

/*
|--------------------------------------------------------------------------
| PREVENT DUPLICATE RELEASE
|--------------------------------------------------------------------------
*/

$dupstmt = $conn->prepare("
    SELECT id FROM thirteenth_month_release
    WHERE employee_id = ? AND release_year = ? AND release_type = ?
");
$dupstmt->bind_param('iis', $empid, $year, $type);
$dupstmt->execute();

if($dupstmt->get_result()->num_rows > 0){
    $_SESSION['error'] = $type.' already released for '.$year;
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

/*
|--------------------------------------------------------------------------
| COMPUTE RELEASE AMOUNT
|--------------------------------------------------------------------------
*/

$amount = 0;

if($mode == 'split'){

    if($type == 'Midyear'){
        $amount = $thirteenthMonth * ($midyearPercent / 100);
    }
    else{
        // Year End pays the remaining balance
        $amount = $thirteenthMonth - $totalReleased;
    }
}
else{

    if($type == 'Midyear'){
        $_SESSION['error'] = 'Midyear release is disabled';
        header('location: thirteenth_month.php?year='.$year);
        exit();
    }

    $amount = $thirteenthMonth - $totalReleased;
}

/*
|--------------------------------------------------------------------------
| VALIDATION — never release more than the remaining balance
|--------------------------------------------------------------------------
*/

$remaining = $thirteenthMonth - $totalReleased;

if($amount > $remaining){
    $amount = $remaining;       // clamp (e.g. a >100% midyear setting can't over-pay)
}

if($amount <= 0){
    $_SESSION['error'] = 'No remaining 13th month balance for '.$year;
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

/*
|--------------------------------------------------------------------------
| INSERT RELEASE
|--------------------------------------------------------------------------
*/

$amount = round($amount, 2);

$instmt = $conn->prepare("
    INSERT INTO thirteenth_month_release
    (employee_id, release_year, release_date, amount, release_type)
    VALUES (?, ?, CURDATE(), ?, ?)
");
$instmt->bind_param('iids', $empid, $year, $amount, $type);

if($instmt->execute()){
    $_SESSION['success'] =
        '13th Month '.$type.' released successfully for '.$year.
        '. Amount: &#8369;'.number_format($amount, 2);
}
else{
    $_SESSION['error'] = 'Could not record the release. Please try again.';
}

header('location: thirteenth_month.php?year='.$year);
exit();
?>

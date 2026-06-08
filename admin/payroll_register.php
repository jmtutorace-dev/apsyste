<?php
include 'includes/session.php';
include 'includes/payslip_compute.php';   // shared compute_payslip()

if(empty($_POST['date_range'])){
    $_SESSION['error'] = 'Please choose a pay period first.';
    header('location: payroll.php');
    exit();
}

$ex   = explode(' - ', $_POST['date_range']);
$from = date('Y-m-d', strtotime($ex[0]));
$to   = date('Y-m-d', strtotime($ex[1]));
$tag  = $from.'_to_'.$to;

// Employees with attendance in the period (same set as the payslip/payroll PDFs)
$stmt = $conn->prepare(
    "SELECT DISTINCT attendance.employee_id AS empid
     FROM attendance
     LEFT JOIN employees ON employees.id = attendance.employee_id
     WHERE attendance.date BETWEEN ? AND ?
     ORDER BY employees.lastname ASC, employees.firstname ASC"
);
$stmt->bind_param('ss', $from, $to);
$stmt->execute();
$res = $stmt->get_result();

// Discard any stray output, then stream the CSV
while(ob_get_level() > 0){ ob_end_clean(); }

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="payroll_register_'.$tag.'.csv"');

$out = fopen('php://output', 'w');

function money($v){ return number_format((float)$v, 2, '.', ''); }   // no thousands sep → CSV-safe

fputcsv($out, array('PAYROLL REGISTER', $from.' to '.$to));
fputcsv($out, array());
fputcsv($out, array(
    'Employee ID','Last Name','First Name','Monthly Salary','Basic Salary',
    'Overtime','Total Earnings',
    'Absences','Late','Withholding Tax','SSS','PhilHealth','Pag-IBIG','Cash Advance',
    'Total Deductions','Net Pay'
));

$tEarn = 0; $tDed = 0; $tNet = 0;

while($r = $res->fetch_assoc()){

    $p = compute_payslip($conn, $r['empid'], $from, $to);
    if(!$p['found']){ continue; }

    $e = $p['employee'];

    fputcsv($out, array(
        $e['employee_id'], $e['lastname'], $e['firstname'],
        money($p['monthly']),
        money($p['earn']['BASIC SALARY']),
        money($p['earn']['OVERTIME PAY (OT)']),
        money($p['total_earnings']),
        money($p['ded']['ABSENCES']),
        money($p['ded']['LATE']),
        money($p['ded']['WITHHOLDING TAX']),
        money($p['ded']['SSS CONTRIBUTION']),
        money($p['ded']['PHIC CONTRIBUTION']),
        money($p['ded']['PAG-IBIG CONTRIBUTION']),
        money($p['ded']['CASH ADVANCE']),
        money($p['total_deductions']),
        money($p['net'])
    ));

    $tEarn += $p['total_earnings'];
    $tDed  += $p['total_deductions'];
    $tNet  += $p['net'];
}

fputcsv($out, array());
fputcsv($out, array('TOTALS','','','','','', money($tEarn),'','','','','','','', money($tDed), money($tNet)));

fclose($out);
exit();
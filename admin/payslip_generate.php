<?php
ob_start();   // capture stray output from includes so TCPDF can still send the PDF
include 'includes/session.php';
include 'includes/payslip_compute.php';
include 'includes/payslip_render.php';

/* ------------------------------------------------------------------ */
/*  Period                                                            */
/* ------------------------------------------------------------------ */

if(empty($_POST['date_range'])){
    $_SESSION['error'] = 'Please choose a pay period first.';
    header('location: payroll.php');
    exit();
}

$range = $_POST['date_range'];

$ex = explode(' - ', $range);

$from = date('Y-m-d', strtotime($ex[0]));
$to   = date('Y-m-d', strtotime($ex[1]));

$period_title = payslip_period_title($ex[0], $ex[1]);
$cfg = get_payroll_settings($conn);

/* ------------------------------------------------------------------ */
/*  Which employees to print (those with attendance in the period)    */
/* ------------------------------------------------------------------ */

$empsql = "SELECT DISTINCT attendance.employee_id AS empid
           FROM attendance
           LEFT JOIN employees ON employees.id = attendance.employee_id
           WHERE attendance.date BETWEEN '$from' AND '$to'
           ORDER BY employees.lastname ASC, employees.firstname ASC";

$empquery = $conn->query($empsql);

$emp_ids = array();
if($empquery){
    while($r = $empquery->fetch_assoc()){
        $emp_ids[] = $r['empid'];
    }
}

/* ------------------------------------------------------------------ */
/*  PDF                                                               */
/* ------------------------------------------------------------------ */

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Employee Payslip');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(12, 10, 12);
$pdf->SetAutoPageBreak(TRUE, 12);
$pdf->SetFont('helvetica', '', 8);

if(count($emp_ids) == 0){

    $pdf->AddPage();
    $pdf->writeHTML('<p align="center">No attendance found for this period.</p>', true, false, true, false, '');

}else{

    foreach($emp_ids as $empid){

        $p = compute_payslip($conn, $empid, $from, $to);

        if(!$p['found']){
            continue;
        }

        $pdf->AddPage();
        $pdf->writeHTML(render_payslip_html($p, $period_title, $cfg['company_name']), true, false, true, false, '');
    }
}

while(ob_get_level() > 0){ ob_end_clean(); }   // drop any stray whitespace/notices
$pdf->Output('payslip.pdf', 'I');
?>

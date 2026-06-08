<?php
ob_start();   // capture stray output from includes so TCPDF can still send the PDF
include 'includes/session.php';   // loads $emp + enforces login
include __DIR__ . '/../admin/includes/payslip_compute.php';
include __DIR__ . '/../admin/includes/payslip_render.php';

if(empty($_POST['date_range'])){
    header('location: payslip.php');
    exit();
}

$ex = explode(' - ', $_POST['date_range']);

$from = date('Y-m-d', strtotime($ex[0]));
$to   = date('Y-m-d', strtotime($ex[1]));

$period_title = payslip_period_title($ex[0], $ex[1]);
$cfg = get_payroll_settings($conn);

// Compute ONLY for the logged-in employee — they can never see anyone else's slip
$p = compute_payslip($conn, $emp['id'], $from, $to);

require_once(__DIR__ . '/../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('My Payslip');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(12, 10, 12);
$pdf->SetAutoPageBreak(TRUE, 12);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage();

if($p['found']){
    $pdf->writeHTML(render_payslip_html($p, $period_title, $cfg['company_name']), true, false, true, false, '');
}else{
    $pdf->writeHTML('<p align="center">No payslip data available for this period.</p>', true, false, true, false, '');
}

while(ob_get_level() > 0){ ob_end_clean(); }   // drop any stray whitespace/notices
$pdf->Output('my_payslip.pdf', 'I');
?>
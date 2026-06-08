<?php
ob_start();   // capture stray output from includes so TCPDF can still send the PDF
include 'includes/session.php';
include 'includes/payslip_compute.php';   // shared compute_payslip() — SAME math as the payslip

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

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title   = date('M d, Y', strtotime($ex[1]));

/* ------------------------------------------------------------------ */
/*  Employees with attendance in the period (same set as the payslip) */
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
/*  Render helpers                                                    */
/* ------------------------------------------------------------------ */

function pr_amt($v){
    return ($v > 0.0001) ? number_format($v, 2) : '-';
}

/* non-zero line items for one side of a card */
function pr_lines($labels, $amounts){
    $html = '';
    foreach($labels as $l){
        if($amounts[$l] > 0.0001){
            $html .= '<tr>
                        <td style="font-size:8px;">'.$l.'</td>
                        <td align="right" style="font-size:8px;">'.number_format($amounts[$l], 2).'</td>
                      </tr>';
        }
    }
    if($html === ''){
        $html = '<tr><td colspan="2" style="font-size:8px; color:#999;">—</td></tr>';
    }
    return $html;
}

function render_payroll_card($p){

    global $PAYSLIP_EARNING_ROWS, $PAYSLIP_DEDUCTION_ROWS;

    $emp  = $p['employee'];
    $name = trim($emp['lastname'].', '.$emp['firstname']);

    $earn_lines = pr_lines($PAYSLIP_EARNING_ROWS,   $p['earn']);
    $ded_lines  = pr_lines($PAYSLIP_DEDUCTION_ROWS, $p['ded']);

    return '
    <table cellpadding="4" cellspacing="0" style="width:100%; border:1px solid #888; margin-bottom:14px;">

        <tr>
            <td colspan="2" style="background-color:#2c3e50; color:#fff; font-size:10px; font-weight:bold;">
                '.$name.'
            </td>
            <td colspan="2" align="right" style="background-color:#2c3e50; color:#fff; font-size:8px;">
                Employee ID: '.$emp['employee_id'].'
            </td>
        </tr>

        <tr style="background-color:#eee; font-size:8px; font-weight:bold;">
            <td colspan="2">EARNINGS</td>
            <td colspan="2">DEDUCTIONS</td>
        </tr>

        <tr>
            <td colspan="2" style="vertical-align:top;">
                <table cellpadding="2" cellspacing="0" style="width:100%;">'.$earn_lines.'</table>
            </td>
            <td colspan="2" style="vertical-align:top;">
                <table cellpadding="2" cellspacing="0" style="width:100%;">'.$ded_lines.'</table>
            </td>
        </tr>

        <tr style="font-size:8px; font-weight:bold; background-color:#f7f7f7;">
            <td>TOTAL EARNINGS</td>
            <td align="right">'.pr_amt($p['total_earnings']).'</td>
            <td>TOTAL DEDUCTIONS</td>
            <td align="right">'.pr_amt($p['total_deductions']).'</td>
        </tr>

        <tr style="font-size:9px; font-weight:bold; background-color:#27ae60; color:#fff;">
            <td colspan="3">NET PAY</td>
            <td align="right">'.number_format($p['net'], 2).'</td>
        </tr>

    </table>';
}

/* ------------------------------------------------------------------ */
/*  PDF                                                               */
/* ------------------------------------------------------------------ */

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Payroll Summary');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(12, 10, 12);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 9);

$pdf->AddPage();

$content = '
<h2 align="center">ACE PAYROLL SYSTEM</h2>
<h4 align="center">Payroll Summary</h4>
<p align="center">'.$from_title.' - '.$to_title.'</p>
<hr>
';

$grand_total = 0;

if(count($emp_ids) == 0){
    $content .= '<p align="center">No attendance found for this period.</p>';
}else{
    foreach($emp_ids as $empid){

        $p = compute_payslip($conn, $empid, $from, $to);

        if(!$p['found']){
            continue;
        }

        $content .= render_payroll_card($p);
        $grand_total += $p['net'];
    }

    $content .= '
    <table cellpadding="8" cellspacing="0" style="border:2px solid #000; width:100%; margin-top:10px;">
        <tr>
            <td align="right" style="font-size:12px; font-weight:bold;">
                TOTAL NET PAY: PHP '.number_format($grand_total, 2).'
            </td>
        </tr>
    </table>';
}

$pdf->writeHTML($content, true, false, true, false, '');

while(ob_get_level() > 0){ ob_end_clean(); }   // drop any stray whitespace/notices
$pdf->Output('payroll.pdf', 'I');
?>

<?php
ob_start();   // capture stray output from includes so TCPDF can still send the PDF
include 'includes/session.php';
include 'includes/thirteenth_month_compute.php';

require('../tcpdf/tcpdf.php');

if(!isset($_GET['id'])){
    die('Employee ID missing');
}

$empid = intval($_GET['id']);
$year  = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

/*
|--------------------------------------------------------------------------
| COMPUTE (shared, attendance-based)
|--------------------------------------------------------------------------
*/

$tm = compute_thirteenth_month($conn, $empid, $year);

if(!$tm['found']){
    die('Employee not found');
}

$emp = $tm['employee'];

/*
|--------------------------------------------------------------------------
| PDF
|--------------------------------------------------------------------------
*/

$pdf = new TCPDF();

$pdf->SetCreator('ACE Medical Center');
$pdf->SetAuthor('HR Payroll');
$pdf->SetTitle('13th Month Pay');

$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'ACE MEDICAL CENTER', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, '13th Month Pay Report - '.$year, 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);

$html = '

<table border="1" cellpadding="5">

<tr>
<td width="40%"><b>Employee ID</b></td>
<td width="60%">'.$emp['employee_id'].'</td>
</tr>

<tr>
<td><b>Employee Name</b></td>
<td>'.$emp['lastname'].', '.$emp['firstname'].'</td>
</tr>

<tr>
<td><b>Date Hired</b></td>
<td>'.date('F d, Y', strtotime($tm['date_hired'])).'</td>
</tr>

<tr>
<td><b>Monthly Salary</b></td>
<td>PHP '.number_format($tm['monthly_salary'], 2).'</td>
</tr>

<tr>
<td><b>Months Worked ('.$year.')</b></td>
<td>'.$tm['months_worked'].'</td>
</tr>

<tr>
<td><b>Basic Salary Earned ('.$year.')</b></td>
<td>PHP '.number_format($tm['basic_earned'], 2).'</td>
</tr>

<tr>
<td><b>13th Month Pay</b></td>
<td><b>PHP '.number_format($tm['thirteenth_month'], 2).'</b></td>
</tr>

<tr>
<td><b>Total Released ('.$year.')</b></td>
<td>PHP '.number_format($tm['released'], 2).'</td>
</tr>

<tr>
<td><b>Remaining Balance</b></td>
<td><b>PHP '.number_format($tm['balance'], 2).'</b></td>
</tr>

</table>

';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Ln(4);

$pdf->SetFont('helvetica', 'I', 8);
$pdf->writeHTML(
    '13th Month Pay = Total basic salary actually earned during '.$year.
    ' divided by 12 (PD 851 / DOLE). Computed from recorded attendance.',
    true, false, true, false, ''
);

$pdf->Ln(6);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Release History', 0, 1);

$pdf->SetFont('helvetica', '', 10);

$history = '
<table border="1" cellpadding="4">

<tr>
<th width="25%"><b>Date</b></th>
<th width="25%"><b>Type</b></th>
<th width="25%"><b>Year</b></th>
<th width="25%"><b>Amount</b></th>
</tr>
';

$hsql = "
    SELECT *
    FROM thirteenth_month_release
    WHERE employee_id = '$empid'
    ORDER BY release_date DESC, id DESC
";

$hquery = $conn->query($hsql);

if($hquery){
    while($hrow = $hquery->fetch_assoc()){

        $history .= '
        <tr>
            <td>'.$hrow['release_date'].'</td>
            <td>'.$hrow['release_type'].'</td>
            <td>'.$hrow['release_year'].'</td>
            <td>PHP '.number_format($hrow['amount'], 2).'</td>
        </tr>
        ';
    }
}

$history .= '</table>';

$pdf->writeHTML($history, true, false, true, false, '');

while(ob_get_level() > 0){ ob_end_clean(); }   // drop any stray whitespace/notices
$pdf->Output('13th_Month_'.$emp['employee_id'].'_'.$year.'.pdf', 'I');
?>

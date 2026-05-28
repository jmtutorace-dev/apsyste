<?php
include 'includes/session.php';

$range = $_POST['date_range'];

$ex = explode(' - ', $range);

$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

// =====================================================
// GLOBAL DEDUCTIONS
// =====================================================

$sql = "SELECT SUM(amount) AS total_amount FROM deductions";

$query = $conn->query($sql);

$drow = $query->fetch_assoc();

$deduction = $drow['total_amount'] ? $drow['total_amount'] : 0;

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);

$pdf->SetTitle('Payslip: '.$from_title.' - '.$to_title);

$pdf->setPrintHeader(false);

$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10);

$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->SetFont('helvetica', '', 11);

$pdf->AddPage();

$contents = '';

// =====================================================
// EMPLOYEES
// =====================================================

$sql = "SELECT *,
               SUM(num_hr) AS total_hr,
               attendance.employee_id AS empid,
               employees.employee_id AS employee

        FROM attendance

        LEFT JOIN employees
          ON employees.id = attendance.employee_id

        LEFT JOIN position
          ON position.id = employees.position_id

        LEFT JOIN schedules
          ON schedules.id = employees.schedule_id

        WHERE date BETWEEN '$from' AND '$to'

        GROUP BY attendance.employee_id

        ORDER BY employees.lastname ASC,
                 employees.firstname ASC";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

    $empid = $row['empid'];

    // =====================================================
    // CASH ADVANCE
    // =====================================================

    $casql = "SELECT SUM(amount) AS cashamount
              FROM cashadvance
              WHERE employee_id='$empid'
              AND date_advance BETWEEN '$from' AND '$to'";

    $caquery = $conn->query($casql);

    $carow = $caquery->fetch_assoc();

    $cashadvance = $carow['cashamount'] ? $carow['cashamount'] : 0;

    // =====================================================
    // OVERTIME
    // =====================================================

    $otsql = "SELECT SUM(hours * rate) AS overtime_pay
              FROM overtime
              WHERE employee_id='$empid'
              AND date_overtime BETWEEN '$from' AND '$to'";

    $otquery = $conn->query($otsql);

    $otrow = $otquery->fetch_assoc();

    $overtime = $otrow['overtime_pay'] ? $otrow['overtime_pay'] : 0;

    // =====================================================
    // MONTHLY SALARY
    // =====================================================

    $monthly_salary = $row['rate'] ? $row['rate'] : 0;

    // =====================================================
    // REAL PAYROLL COMPUTATION
    // =====================================================

    /*
        REAL PAYROLL LOGIC

        Employee only earns based on:
        total worked hours

        FULL ATTENDANCE
        = FULL SEMI-MONTHLY PAY
    */

    $hourly_rate = ($monthly_salary / 2) / (15 * 8);

    // ACTUAL BASE PAY
    $base_pay = $row['total_hr'] * $hourly_rate;

    // =====================================================
    // LATE / UNDERTIME
    // =====================================================

    /*
        DISPLAY ONLY

        NOT DEDUCTED AGAIN
        because undertime is already reflected
        in reduced worked hours.
    */

    $late_deduction = 0;

    $attsql = "SELECT *
               FROM attendance
               WHERE employee_id='$empid'
               AND date BETWEEN '$from' AND '$to'";

    $attquery = $conn->query($attsql);

    while($attrow = $attquery->fetch_assoc()){

        $required_hours = 8;

        $worked_hours = $attrow['num_hr'];

        if($worked_hours < $required_hours){

            $undertime_hours = $required_hours - $worked_hours;

            $late_deduction += ($undertime_hours * $hourly_rate);
        }
    }

    // =====================================================
    // GROSS PAY
    // =====================================================

    $gross = $base_pay + $overtime;

    // =====================================================
    // TOTAL DEDUCTIONS
    // =====================================================

    /*
        DO NOT INCLUDE late deduction AGAIN
        to avoid DOUBLE DEDUCTION
    */

    $total_deduction = $deduction + $cashadvance;

    // =====================================================
    // NET PAY
    // =====================================================

    $net = $gross - $total_deduction;

    // =====================================================
    // PDF CONTENT
    // =====================================================

    $contents .= '

    <h2 align="center">PAYSLIP</h2>

    <h4 align="center">'.$from_title.' - '.$to_title.'</h4>

    <table cellspacing="0" cellpadding="3">

        <tr>

            <td width="25%" align="right">
                Employee Name:
            </td>

            <td width="25%">
                <b>'.$row['firstname'].' '.$row['lastname'].'</b>
            </td>

            <td width="25%" align="right">
                Monthly Salary:
            </td>

            <td width="25%" align="right">
                '.number_format($monthly_salary, 2).'
            </td>

        </tr>

        <tr>

            <td width="25%" align="right">
                Employee ID:
            </td>

            <td width="25%">
                '.$row['employee'].'
            </td>

            <td width="25%" align="right">
                Hours Worked:
            </td>

            <td width="25%" align="right">
                '.number_format($row['total_hr'], 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                Base Pay:
            </td>

            <td width="25%" align="right">
                '.number_format($base_pay, 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                Overtime Pay:
            </td>

            <td width="25%" align="right">
                '.number_format($overtime, 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                Late / Undertime:
            </td>

            <td width="25%" align="right">
                '.number_format($late_deduction, 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                Company Deductions:
            </td>

            <td width="25%" align="right">
                '.number_format($deduction, 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                Cash Advance:
            </td>

            <td width="25%" align="right">
                '.number_format($cashadvance, 2).'
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                <b>Gross Pay:</b>
            </td>

            <td width="25%" align="right">
                <b>'.number_format($gross, 2).'</b>
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                <b>Total Deduction:</b>
            </td>

            <td width="25%" align="right">
                <b>'.number_format($total_deduction, 2).'</b>
            </td>

        </tr>

        <tr>

            <td></td>
            <td></td>

            <td width="25%" align="right">
                <b>Net Pay:</b>
            </td>

            <td width="25%" align="right">
                <b>'.number_format($net, 2).'</b>
            </td>

        </tr>

    </table>

    <br><hr>
    ';
}

$pdf->writeHTML($contents);

$pdf->Output('payslip.pdf', 'I');
?>
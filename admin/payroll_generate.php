<?php
include 'includes/session.php';

function compute_tax($salary){

    $tax = 0;

    if ($salary <= 10417) {

        $tax = 0;

    } elseif ($salary <= 16666) {

        $tax = ($salary - 10417) * 0.15;

    } elseif ($salary <= 33332) {

        $tax = 937.50 + (($salary - 16667) * 0.20);

    } elseif ($salary <= 83332) {

        $tax = 4270.70 + (($salary - 33333) * 0.25);

    } elseif ($salary <= 333332) {

        $tax = 16770.70 + (($salary - 83333) * 0.30);

    } else {

        $tax = 91770.70 + (($salary - 333333) * 0.35);
    }

    return round($tax, 2);
}

function generatePayrollCards($from, $to, $conn){

    $contents = '';

    $sql = "SELECT *,
                   attendance.employee_id AS empid,
                   position.rate

            FROM attendance

            LEFT JOIN employees
                ON employees.id = attendance.employee_id

            LEFT JOIN position
                ON position.id = employees.position_id

            WHERE attendance.date BETWEEN '$from' AND '$to'

            GROUP BY attendance.employee_id

            ORDER BY employees.lastname ASC,
                     employees.firstname ASC";

    $query = $conn->query($sql);

    $grand_total = 0;

    while($row = $query->fetch_assoc()){

        $empid = $row['empid'];

        // =========================================
        // SALARY
        // =========================================

        $monthly_salary = isset($row['rate'])
                         ? $row['rate']
                         : 0;

        $daily_rate = $monthly_salary / 26;

        $hourly_rate = $daily_rate / 8;

        // =========================================
        // ATTENDANCE
        // =========================================

        $days_present = 0;
        $worked_hours = 0;
        $late_deduction = 0;
        $holiday_pay = 0;

        $attsql = "SELECT *
                   FROM attendance
                   WHERE employee_id='$empid'
                   AND date BETWEEN '$from' AND '$to'";

        $attquery = $conn->query($attsql);

        while($attrow = $attquery->fetch_assoc()){

            $hrs = $attrow['num_hr'];
            $attendance_date = $attrow['date'];

            if($hrs <= 0){
                continue;
            }

            $days_present++;

            $worked_hours += $hrs;

            // =========================================
            // UNDERTIME
            // =========================================

            if($hrs < 8){

                $undertime = 8 - $hrs;

                $late_deduction += (
                    $undertime * $hourly_rate
                );
            }

            // =========================================
// HOLIDAY DOUBLE PAY
// =========================================

$hsql = "SELECT *
         FROM holidays
         WHERE holiday_date='$attendance_date'";

$hquery = $conn->query($hsql);

if($hquery->num_rows > 0){

    $holiday = $hquery->fetch_assoc();

    $holiday_type = strtolower(trim($holiday['type']));

    // REGULAR HOLIDAY
    if(
        $holiday_type == 'regular' ||
        $holiday_type == 'regular holiday'
    ){

        if($hrs > 0){

            /*
             Employee worked on holiday.
             Base pay already contains normal pay.
             Add another 100% so total becomes DOUBLE PAY.
            */

            $holiday_pay += (
    ($hrs * $hourly_rate) * 2
);

        }else{

            $holiday_pay += $daily_rate;
        }
    }

    // SPECIAL HOLIDAY
    else if(
        $holiday_type == 'special' ||
        $holiday_type == 'special holiday'
    ){

        if($hrs > 0){

            $holiday_pay += (
                ($hrs * $hourly_rate) * 0.30
            );
        }
    }

} // <-- THIS BRACE WAS MISSING
        }

        // =========================================
        // HOLIDAY PAY EVEN WITHOUT TIME IN
        // =========================================

        $holidaysql = "SELECT *
                       FROM holidays
                       WHERE holiday_date BETWEEN '$from' AND '$to'";

        $holidayquery = $conn->query($holidaysql);

        while($holidayrow = $holidayquery->fetch_assoc()){

            $holiday_date = $holidayrow['holiday_date'];

            $checksql = "SELECT *
                         FROM attendance
                         WHERE employee_id='$empid'
                         AND date='$holiday_date'";

            $checkquery = $conn->query($checksql);

            if(
                $checkquery->num_rows == 0 &&
                strtolower($holidayrow['type']) == 'regular'
            ){

                $holiday_pay += $daily_rate;
            }
        }

        // =========================================
        // BASE PAY
        // =========================================

        $base_pay = 0;

$attsql2 = "SELECT *
            FROM attendance
            WHERE employee_id='$empid'
            AND date BETWEEN '$from' AND '$to'";

$attquery2 = $conn->query($attsql2);

while($attrow2 = $attquery2->fetch_assoc()){

    $hrs2 = $attrow2['num_hr'];
    $date2 = $attrow2['date'];

    $isHoliday = false;

    $hsql2 = "SELECT *
              FROM holidays
              WHERE holiday_date='$date2'";

    $hquery2 = $conn->query($hsql2);

    if($hquery2->num_rows > 0){
        $isHoliday = true;
    }

    if(!$isHoliday){
        $base_pay += ($hrs2 * $hourly_rate);
    }
}

        // =========================================
        // OVERTIME
        // =========================================

        $otsql = "SELECT SUM(hours * rate) AS overtime_pay
                  FROM overtime
                  WHERE employee_id='$empid'
                  AND date_overtime BETWEEN '$from' AND '$to'";

        $otquery = $conn->query($otsql);

        $otrow = $otquery->fetch_assoc();

        $overtime = $otrow['overtime_pay']
                   ? $otrow['overtime_pay']
                   : 0;

        // =========================================
        // CASH ADVANCE
        // =========================================

        $casql = "SELECT SUM(amount) AS cashamount
                  FROM cashadvance
                  WHERE employee_id='$empid'
                  AND date_advance BETWEEN '$from' AND '$to'";

        $caquery = $conn->query($casql);

        $carow = $caquery->fetch_assoc();

        $cashadvance = $carow['cashamount']
                      ? $carow['cashamount']
                      : 0;

        // =========================================
        // GOVERNMENT DEDUCTIONS
        // =========================================

        $government_deduction = 0;

        $dsql = "SELECT deductions.*
                 FROM employee_deductions

                 LEFT JOIN deductions
                 ON deductions.id = employee_deductions.deduction_id

                 WHERE employee_deductions.employee_id = '$empid'";

        $dquery = $conn->query($dsql);

        while($drow = $dquery->fetch_assoc()){

            $description = strtolower(trim($drow['description']));

            if(
                $description == 'tax' ||
                $description == 'withholding tax'
            ){
                continue;
            }

            $amount = $drow['amount'];

            $type = strtolower($drow['type']);

            if(
                $type == 'percent' ||
                $type == 'percentage'
            ){

                $computed =
                    $monthly_salary * ($amount / 100);

            } else {

                $computed = $amount;
            }

            $computed = $computed / 2;

            $government_deduction += $computed;
        }
// =========================================
// EMPLOYEE DEDUCTIONS
// =========================================

$employee_deduction = 0;
$employee_deduction_rows = '';

$edsql = "
    SELECT
        description,
        amount,
        created_on
    FROM employee_deductions
    WHERE employee_id = '$empid'
    AND created_on BETWEEN '$from' AND '$to'
    ORDER BY created_on ASC
";

$edquery = $conn->query($edsql);

while($edrow = $edquery->fetch_assoc()){

    $employee_deduction += $edrow['amount'];

    $employee_deduction_rows .= '

    <tr>

        <td width="50%">
            '.$edrow['description'].'
        </td>

        <td width="50%" align="right">
            PHP '.number_format($edrow['amount'],2).'
        </td>

    </tr>

    ';
}
        // =========================================
        // TAX
        // =========================================

        $semi_salary = $monthly_salary / 2;

        $tax = compute_tax($semi_salary);

        // =========================================
        // GROSS PAY
        // =========================================

        $gross = $base_pay + $holiday_pay + $overtime;

        // =========================================
        // TOTAL DEDUCTIONS
        // =========================================

        $total_deduction =
            $government_deduction
            + $employee_deduction
            + $tax
            + $cashadvance
            + $late_deduction;

        // =========================================
        // NET PAY
        // =========================================

        $net = $gross - $total_deduction;

        $grand_total += $net;

        // =========================================
        // PAYROLL CARD
        // =========================================

        $contents .= '

        <table cellpadding="0" cellspacing="0" style="width:100%; margin-bottom:18px;">

            <tr>

                <td>

                    <table cellpadding="7" cellspacing="0"
                           style="
                                border:1px solid #dcdcdc;
                                width:100%;
                           ">

                        <tr>

                            <td width="65%"
                                style="
                                    background-color:#2c3e50;
                                    color:#ffffff;
                                    font-size:13px;
                                    font-weight:bold;
                                ">

                                '.$row['lastname'].', '.$row['firstname'].'

                            </td>

                            <td width="35%"
                                align="right"
                                style="
                                    background-color:#2c3e50;
                                    color:#ffffff;
                                    font-size:10px;
                                ">

                                Employee ID: '.$row['employee_id'].'

                            </td>

                        </tr>

                    </table>

                    <table cellpadding="6" cellspacing="0"
                           style="
                                border-left:1px solid #dcdcdc;
                                border-right:1px solid #dcdcdc;
                                border-bottom:1px solid #dcdcdc;
                                width:100%;
                           ">

                        <tr>

                            <td width="50%">

                                <b>Monthly Salary</b><br>
                                PHP '.number_format($monthly_salary,2).'

                            </td>

                            <td width="50%">

                                <b>Days Present</b><br>
                                '.$days_present.'

                            </td>

                        </tr>

                        <tr>

                            <td width="50%">

                                <b>Worked Hours</b><br>
                                '.number_format($worked_hours,2).'

                            </td>

                            <td width="50%">

                                <b>Overtime Pay</b><br>
                                PHP '.number_format($overtime,2).'

                            </td>

                        </tr>

                        <tr>

                            <td width="50%">

                                <b>Holiday Pay</b><br>
                                PHP '.number_format($holiday_pay,2).'

                            </td>

                            <td width="50%">

                                <b>Base Pay</b><br>
                                PHP '.number_format($base_pay,2).'

                            </td>

                        </tr>

                    </table>

                    <table cellpadding="6" cellspacing="0"
                           style="
                                border-left:1px solid #dcdcdc;
                                border-right:1px solid #dcdcdc;
                                border-bottom:1px solid #dcdcdc;
                                width:100%;
                           ">

                        <tr>

                            <td colspan="2"
                                style="
                                    background-color:#f5f5f5;
                                    font-weight:bold;
                                    font-size:11px;
                                ">

                                DEDUCTIONS

                            </td>

                        </tr>

                        <tr>

                            <td width="50%">
                                Government Deduction
                            </td>

                            <td width="50%" align="right">
                                PHP '.number_format($government_deduction,2).'
                            </td>

                        </tr>

                        <tr>

                            <td width="50%">
                                Tax
                            </td>

                            <td width="50%" align="right">
                                PHP '.number_format($tax,2).'
                            </td>

                        </tr>

                        <tr>

                            <td width="50%">
                                Cash Advance
                            </td>

                            <td width="50%" align="right">
                                PHP '.number_format($cashadvance,2).'
                            </td>

                        </tr>
                        '.$employee_deduction_rows.'
                        <tr>

                            <td width="50%">
                                Late / Undertime
                            </td>

                            <td width="50%" align="right">
                                PHP '.number_format($late_deduction,2).'
                            </td>

                        </tr>

                        <tr>

                            <td width="50%">
                                <b>Total Deduction</b>
                            </td>

                            <td width="50%" align="right">
                                <b>PHP '.number_format($total_deduction,2).'</b>
                            </td>

                        </tr>

                    </table>

                    <table cellpadding="7" cellspacing="0"
                           style="
                                border:1px solid #27ae60;
                                width:100%;
                           ">

                        <tr>

                            <td width="50%"
                                style="
                                    background-color:#27ae60;
                                    color:#ffffff;
                                    font-size:11px;
                                    font-weight:bold;
                                ">

                                NET PAY

                            </td>

                            <td width="50%"
                                align="right"
                                style="
                                    background-color:#27ae60;
                                    color:#ffffff;
                                    font-size:12px;
                                    font-weight:bold;
                                ">

                                PHP '.number_format($net,2).'

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>
        ';
    }

    $contents .= '

    <table cellpadding="8"
           cellspacing="0"
           style="
                border:2px solid #000;
                width:100%;
                margin-top:10px;
           ">

        <tr>

            <td align="right"
                style="
                    font-size:12px;
                    font-weight:bold;
                ">

                TOTAL NET PAY:
                PHP '.number_format($grand_total,2).'

            </td>

        </tr>

    </table>
    ';

    return $contents;
}

$range = $_POST['date_range'];

$ex = explode(' - ', $range);

$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);

$pdf->SetTitle('Payroll Summary');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(12, 10, 12);

$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

$content = '

<h1 align="center">
    ACE PAYROLL SYSTEM
</h1>

<h3 align="center">
    Payroll Summary
</h3>

<p align="center">
    '.$from_title.' - '.$to_title.'
</p>

<hr>

';

$content .= generatePayrollCards($from, $to, $conn);

$pdf->writeHTML($content, true, false, true, false, '');

$pdf->Output('payroll.pdf', 'I');
?>
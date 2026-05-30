<?php
include 'includes/session.php';

$range = $_POST['date_range'];

$ex = explode(' - ', $range);

$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);

$pdf->SetTitle('Employee Payslip');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(12, 10, 12);

$pdf->SetAutoPageBreak(TRUE, 12);

$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

// =====================================================
// TAX FUNCTION
// =====================================================

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

// =====================================================
// EMPLOYEES
// =====================================================

$sql = "SELECT *,
               SUM(num_hr) AS total_hr,
               attendance.employee_id AS empid,
               employees.employee_id AS employee,
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

$contents = '';

while($row = $query->fetch_assoc()){

    $empid = $row['empid'];

    // =====================================================
    // SALARY
    // =====================================================

    $monthly_salary = isset($row['rate'])
                     ? $row['rate']
                     : 0;

    $daily_rate = $monthly_salary / 26;

    $hourly_rate = $daily_rate / 8;

    // =====================================================
    // ATTENDANCE
    // =====================================================

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

        // =====================================================
        // UNDERTIME
        // =====================================================

        if($hrs < 8){

            $undertime = 8 - $hrs;

            $late_deduction += (
                $undertime * $hourly_rate
            );
        }

        // =====================================================
// HOLIDAY PAY
// =====================================================

$hsql = "SELECT *
         FROM holidays
         WHERE DATE(holiday_date)=DATE('$attendance_date')";

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
         Employee worked on regular holiday.
         Give full double pay.
        */

        $holiday_pay += (
            ($hrs * $hourly_rate) * 2
        );

    }else{

        /*
         Employee did not work.
         Still entitled to regular holiday pay.
        */

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
}
    }

    // =====================================================
    // HOLIDAY WITHOUT ATTENDANCE
    // =====================================================

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

        $holiday_type = strtolower(trim($holidayrow['type']));

if(
    $checkquery->num_rows == 0 &&
    (
        $holiday_type == 'regular' ||
        $holiday_type == 'regular holiday'
    )
){
    $holiday_pay += $daily_rate;
}
    }

    // =====================================================
// BASE PAY
// =====================================================

$base_pay = (
    $worked_hours * $hourly_rate
);

    // =====================================================
    // OVERTIME
    // =====================================================

    $otsql = "SELECT SUM(hours * rate) AS overtime_pay
              FROM overtime
              WHERE employee_id='$empid'
              AND date_overtime BETWEEN '$from' AND '$to'";

    $otquery = $conn->query($otsql);

    $otrow = $otquery->fetch_assoc();

    $overtime = $otrow['overtime_pay']
               ? $otrow['overtime_pay']
               : 0;

    // =====================================================
    // CASH ADVANCE
    // =====================================================

    $casql = "SELECT SUM(amount) AS cashamount
              FROM cashadvance
              WHERE employee_id='$empid'
              AND date_advance BETWEEN '$from' AND '$to'";

    $caquery = $conn->query($casql);

    $carow = $caquery->fetch_assoc();

    $cashadvance = $carow['cashamount']
                  ? $carow['cashamount']
                  : 0;

                  // =====================================================
                    // SPECIFIC EMPLOYEE DEDUCTIONS
                    // =====================================================

$employee_deduction = 0;

$employee_deduction_rows = '';

$edsql = "
    SELECT description,
           amount,
           created_on
    FROM employee_deductions
    WHERE employee_id='$empid'
    AND created_on BETWEEN '$from' AND '$to'
    ORDER BY created_on ASC
";

$edquery = $conn->query($edsql);

while($edrow = $edquery->fetch_assoc()){

    $employee_deduction += $edrow['amount'];

    $employee_deduction_rows .= '
        <tr>
            <td>'.$edrow['description'].'</td>
            <td align="right">
                PHP '.number_format($edrow['amount'],2).'
            </td>
        </tr>
    ';
}
    // =====================================================
    // GOVERNMENT DEDUCTIONS
    // =====================================================

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

    // =====================================================
    // TAX
    // =====================================================

    $semi_salary = $monthly_salary / 2;

    $tax = compute_tax($semi_salary);

    // =====================================================
    // GROSS PAY
    // =====================================================

    $gross = $base_pay + $holiday_pay + $overtime;

    // =====================================================
    // TOTAL DEDUCTIONS
    // =====================================================

    $total_deduction =
    $government_deduction
    + $employee_deduction
    + $tax
    + $cashadvance
    + $late_deduction;
    // =====================================================
    // NET PAY
    // =====================================================

    $net = $gross - $total_deduction;

    // =====================================================
    // PAYSLIP DESIGN
    // =====================================================

    $contents .= '

    <table cellpadding="0" cellspacing="0"
           style="
                width:100%;
                border:1px solid #222;
           ">

        <tr>

            <td style="
                background-color:#1e3a5f;
                color:white;
                padding:12px;
            ">

                <table width="100%">

                    <tr>

                        <td width="70%">
                            <span style="font-size:18px; font-weight:bold;">
                                ACE PAYROLL SYSTEM
                            </span>
                            <br>
                            Employee Payslip
                        </td>

                        <td width="30%" align="right">
                            <span style="font-size:11px;">
                                Payroll Period
                            </span>
                            <br>
                            <b>'.$from_title.' - '.$to_title.'</b>
                        </td>

                    </tr>

                </table>

            </td>

        </tr>

        <tr>

            <td style="padding:12px;">

                <table width="100%" cellpadding="4">

                    <tr>

                        <td width="25%">
                            <b>Employee Name</b>
                        </td>

                        <td width="35%">
                            '.$row['firstname'].' '.$row['lastname'].'
                        </td>

                        <td width="20%">
                            <b>Employee ID</b>
                        </td>

                        <td width="20%" align="right">
                            '.$row['employee'].'
                        </td>

                    </tr>

                    <tr>

                        <td width="25%">
                            <b>Monthly Salary</b>
                        </td>

                        <td width="35%">
                            PHP '.number_format($monthly_salary,2).'
                        </td>

                        <td width="20%">
                            <b>Days Present</b>
                        </td>

                        <td width="20%" align="right">
                            '.$days_present.'
                        </td>

                    </tr>

                    <tr>

                        <td width="25%">
                            <b>Total Hours</b>
                        </td>

                        <td width="35%">
                            '.number_format($worked_hours,2).'
                        </td>

                        <td width="20%">
                            <b>Hourly Rate</b>
                        </td>

                        <td width="20%" align="right">
                            PHP '.number_format($hourly_rate,2).'
                        </td>

                    </tr>

                </table>

                <br>

                <table cellpadding="6"
                       cellspacing="0"
                       style="
                            width:100%;
                            border:1px solid #bbb;
                       ">

                    <tr style="background-color:#efefef;">

                        <td width="50%">
                            <b>EARNINGS</b>
                        </td>

                        <td width="50%">
                            <b>DEDUCTIONS</b>
                        </td>

                    </tr>

                    <tr>

                        <td width="50%">

                            <table width="100%" cellpadding="4">

                                <tr>
                                    <td>Base Pay</td>
                                    <td align="right">
                                        PHP '.number_format($base_pay,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>Holiday Pay</td>
                                    <td align="right">
                                        PHP '.number_format($holiday_pay,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>Overtime Pay</td>
                                    <td align="right">
                                        PHP '.number_format($overtime,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Gross Pay</b>
                                    </td>

                                    <td align="right">
                                        <b>
                                            PHP '.number_format($gross,2).'
                                        </b>
                                    </td>
                                </tr>

                            </table>

                        </td>

                        <td width="50%">

                            <table width="100%" cellpadding="4">

                                <tr>
                                    <td>Government Deduction</td>
                                    <td align="right">
                                        PHP '.number_format($government_deduction,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>Tax</td>
                                    <td align="right">
                                        PHP '.number_format($tax,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>Cash Advance</td>
                                    <td align="right">
                                        PHP '.number_format($cashadvance,2).'
                                    </td>
                                </tr>
                                '.$employee_deduction_rows.'

                                <tr>
                                    <td>Late / Undertime</td>
                                    <td align="right">
                                        PHP '.number_format($late_deduction,2).'
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Total Deduction</b>
                                    </td>

                                    <td align="right">
                                        <b>
                                            PHP '.number_format($total_deduction,2).'
                                        </b>
                                    </td>
                                </tr>

                            </table>

                        </td>

                    </tr>

                </table>

                <br>

                <table cellpadding="8"
                       cellspacing="0"
                       style="
                            width:100%;
                            border:1px solid #1f7a3e;
                            background-color:#eaf8ef;
                       ">

                    <tr>

                        <td width="50%"
                            style="
                                font-size:13px;
                                font-weight:bold;
                                color:#1f7a3e;
                            ">

                            NET PAY

                        </td>

                        <td width="50%"
                            align="right"
                            style="
                                font-size:14px;
                                font-weight:bold;
                                color:#1f7a3e;
                            ">

                            PHP '.number_format($net,2).'

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <div style="height:35px;"></div>

    ';
}

$pdf->writeHTML($contents, true, false, true, false, '');

$pdf->Output('payslip.pdf', 'I');

?>
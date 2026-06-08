<?php
/*
|--------------------------------------------------------------------------
| 13TH MONTH PAY COMPUTATION  (Philippine law - PD 851 + DOLE)
|--------------------------------------------------------------------------
|
| Rule:  13th Month Pay = (Total BASIC salary actually EARNED during the
|        calendar year) / 12
|
| "Basic salary" excludes overtime, holiday premium, night-shift
| differential and allowances (DOLE guidelines). We therefore take the
| ACTUAL basic pay the employee earned from the `attendance` table --
| the SAME basis your payroll uses -- so absences / unpaid days correctly
| lower the amount and new hires / resigned staff are pro-rated.
|
| Basic pay basis (mirrors payroll_generate.php):
|     daily_rate  = position.rate / 26
|     hourly_rate = daily_rate / 8
|     basic earned (per day) = MIN(hours_worked, 8) * hourly_rate
|         -> capping at 8 hrs/day excludes overtime from "basic salary".
|
| Returns an associative array. Call it from every 13th-month page so the
| numbers are computed in ONE place only.
|
*/

function compute_thirteenth_month($conn, $empid, $year){

    $empid = intval($empid);
    $year  = intval($year);

    $from = $year.'-01-01';
    $to   = $year.'-12-31';

    $data = array(
        'found'            => false,
        'employee'         => null,
        'year'             => $year,
        'from'             => $from,
        'to'               => $to,
        'date_hired'       => null,
        'monthly_salary'   => 0,
        'daily_rate'       => 0,
        'hourly_rate'      => 0,
        'basic_hours'      => 0,
        'basic_earned'     => 0,
        'months_worked'    => 0,
        'entitled'         => false,
        'thirteenth_month' => 0,
        'released'         => 0,
        'balance'          => 0,
    );

    /* ---- Employee + rate ---- */

    $sql = "
        SELECT employees.*, position.rate
        FROM employees
        LEFT JOIN position ON position.id = employees.position_id
        WHERE employees.id = '$empid'
    ";

    $q = $conn->query($sql);

    if(!$q || $q->num_rows == 0){
        return $data;
    }

    $emp = $q->fetch_assoc();

    $data['found']      = true;
    $data['employee']   = $emp;
    $data['date_hired'] = $emp['created_on'];

    $monthly = !empty($emp['rate']) ? $emp['rate'] : 0;
    $daily   = $monthly / 26;
    $hourly  = $daily / 8;

    $data['monthly_salary'] = $monthly;
    $data['daily_rate']     = $daily;
    $data['hourly_rate']    = $hourly;

    /* ---- Actual BASIC hours earned this year (cap 8/day = no OT) ---- */

    $asql = "
        SELECT SUM(LEAST(num_hr, 8)) AS basic_hours
        FROM attendance
        WHERE employee_id = '$empid'
        AND num_hr > 0
        AND date BETWEEN '$from' AND '$to'
    ";

    $aq   = $conn->query($asql);
    $arow = $aq ? $aq->fetch_assoc() : null;

    $basic_hours = ($arow && !empty($arow['basic_hours'])) ? $arow['basic_hours'] : 0;
    $basic_earned = $basic_hours * $hourly;

    $data['basic_hours']  = $basic_hours;
    $data['basic_earned'] = $basic_earned;

    /* ---- 13th month = basic earned / 12 ---- */

    $thirteenth = $basic_earned / 12;
    $data['thirteenth_month'] = $thirteenth;

    /* ---- Months of service within the year (entitlement) ---- */

    $hireTs   = strtotime($emp['created_on']);
    $hireYear = (int) date('Y', $hireTs);

    if($hireYear < $year){
        $monthsWorked = 12;
    }
    elseif($hireYear == $year){
        $hireMonth = (int) date('n', $hireTs);
        $monthsWorked = (12 - $hireMonth) + 1;
    }
    else{
        $monthsWorked = 0; // hired after this year
    }

    if($monthsWorked < 0){
        $monthsWorked = 0;
    }

    $data['months_worked'] = $monthsWorked;

    // Legally entitled = rank-and-file who served at least 1 month in the
    // year. (The payable amount may still be 0 until attendance is recorded.)
    $data['entitled'] = ($monthsWorked >= 1);

    /* ---- Already released this year ---- */

    $rsql = "
        SELECT SUM(amount) AS released
        FROM thirteenth_month_release
        WHERE employee_id = '$empid'
        AND release_year = '$year'
    ";

    $rq   = $conn->query($rsql);
    $rrow = $rq ? $rq->fetch_assoc() : null;

    $released = ($rrow && !empty($rrow['released'])) ? $rrow['released'] : 0;

    $data['released'] = $released;
    $data['balance']  = $thirteenth - $released;

    return $data;
}
?>

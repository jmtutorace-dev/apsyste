<?php
/*
|--------------------------------------------------------------------------
| ACE BUTUAN SEMI-MONTHLY PAYSLIP COMPUTATION
|--------------------------------------------------------------------------
|
| Single shared source of truth for the payslip. Call
| compute_payslip($conn, $empid, $from, $to) from every payslip/payroll page
| so the math lives in ONE place only (same convention as
| thirteenth_month_compute.php). It returns every earning / deduction line
| item keyed by the EXACT label printed on the ACE Butuan manual payslip
| form, plus the period totals and net pay.
|
| MODEL (reverse-engineered from the ACE Butuan paper payslip):
|
|   monthly = position.rate          (position.rate is the MONTHLY salary)
|   daily   = monthly / 26
|   hourly  = daily / 8
|   minute  = hourly / 60
|
|   BASIC SALARY    = monthly / 2          fixed semi-monthly basic (NOT hours-based)
|   ABSENCES        = absent_days * daily  expected workdays - present days
|                                          (rest day = Sunday; paid holidays excluded)
|   LATE            = late_minutes * minute time_in later than schedule.time_in
|   UNDERTIME       = undertime_min * minute time_out earlier than schedule.time_out
|   WITHHOLDING TAX = compute_tax(monthly / 2)
|
| Holiday premium, overtime, government contributions and department/other
| deductions are filled from the existing tables where data exists. The
| remaining itemized lines on the form (night differential, rest-day,
| SSS/Pag-IBIG loans, ACE Coop, donation, adjustments, ...) render as 0
| until their data entry is wired up in a later step.
|
*/

if(!function_exists('compute_tax')){
    include __DIR__.'/tax.php';
}

if(!function_exists('get_payroll_settings')){
    include __DIR__.'/settings.php';
}

/* ------------------------------------------------------------------ */
/*  Exact form rows -- order matters, the renderer iterates these.    */
/* ------------------------------------------------------------------ */

$PAYSLIP_EARNING_ROWS = array(
    'BASIC SALARY',
    'ALLOWANCE',
    'OVERTIME PAY (OT)',
    'NIGHT DIFFERENTIAL (ND)',
    'OVERTIME PAY ND',
    'REST DAY',
    'REST DAY OT',
    'REST DAY ND',
    'REST DAY OT ND',
    'SPECIAL HOLIDAY',
    'SPECIAL HOLIDAY OT',
    'SPECIAL HOLIDAY ND',
    'SPECIAL HOLIDAY OT ND',
    'REGULAR HOLIDAY',
    'REGULAR HOLIDAY OT',
    'REGULAR HOLIDAY ND',
    'REGULAR HOLIDAY OT ND',
    'SPECIAL HOLIDAY REST',
    'SPECIAL HOLIDAY REST OT',
    'SPECIAL HOLIDAY REST ND',
    'SPECIAL HOLIDAY REST OT ND',
    'REGULAR HOLIDAY REST',
    'REGULAR HOLIDAY REST OT',
    'REGULAR HOLIDAY REST ND',
    'REGULAR HOLIDAY REST OT ND',
    'OTHER ALLOWANCES',
    'SALARY ADJUSTMENT',
    'OVERTIME ADJUSTMENT',
);

$PAYSLIP_DEDUCTION_ROWS = array(
    'ABSENCES',
    'LATE',
    'UNDERTIME',
    'WITHHOLDING TAX',
    'SSS CONTRIBUTION',
    'SSS SALARY LOAN',
    'SSS CALAMITY LOAN',
    'PAG-IBIG CONTRIBUTION',
    'PAG-IBIG MPL LOAN',
    'PAG-IBIG CALAMITY LOAN',
    'PAG-IBIG MP2',
    'PHIC CONTRIBUTION',
    'CAFETERIA',
    'SALARY ADJUSTMENT',
    'OVERTIME ADJUSTMENT',
    'CARDIOPULMONARY',
    'CENTRAL SUPPLY ROOM',
    'IMAGING',
    'LABORATORY',
    'MISCELLANEOUS',
    'NEUROSCIENCE',
    'PHARMACY',
    'HOSPITAL BILL',
    'CASH ADVANCE',
    'ACE COOP',
    'DONATION',
);

function compute_payslip($conn, $empid, $from, $to){

    global $PAYSLIP_EARNING_ROWS, $PAYSLIP_DEDUCTION_ROWS;

    $empid = intval($empid);

    /* ---- zero-initialise every form line ---- */

    $earn = array();  $earn_hours = array();
    foreach($PAYSLIP_EARNING_ROWS as $r){ $earn[$r] = 0; $earn_hours[$r] = 0; }

    $ded = array();   $ded_count = array();
    foreach($PAYSLIP_DEDUCTION_ROWS as $r){ $ded[$r] = 0; $ded_count[$r] = 0; }

    $data = array(
        'found'            => false,
        'employee'         => null,
        'from'             => $from,
        'to'               => $to,
        'monthly'          => 0,
        'daily'            => 0,
        'hourly'           => 0,
        'minute'           => 0,
        'earn'             => $earn,
        'earn_hours'       => $earn_hours,
        'ded'              => $ded,
        'ded_count'        => $ded_count,
        'total_earnings'   => 0,
        'total_deductions' => 0,
        'net'              => 0,
    );

    /* ---- employee + monthly rate + work schedule ---- */

    $sql = "SELECT employees.*,
                   position.rate,
                   schedules.time_in  AS sched_in,
                   schedules.time_out AS sched_out
            FROM employees
            LEFT JOIN position  ON position.id  = employees.position_id
            LEFT JOIN schedules ON schedules.id = employees.schedule_id
            WHERE employees.id = '$empid'";

    $q = $conn->query($sql);

    if(!$q || $q->num_rows == 0){
        return $data;
    }

    $emp = $q->fetch_assoc();

    $data['found']    = true;
    $data['employee'] = $emp;

    $cfg          = get_payroll_settings($conn);
    $rest_day     = (int) $cfg['rest_day'];
    $working_days = (int) $cfg['working_days'];
    if($working_days < 1){ $working_days = 26; }

    $monthly = !empty($emp['rate']) ? $emp['rate'] : 0;
    $daily   = $monthly / $working_days;
    $hourly  = $daily / 8;
    $minute  = $hourly / 60;

    $data['monthly'] = $monthly;
    $data['daily']   = $daily;
    $data['hourly']  = $hourly;
    $data['minute']  = $minute;

    $sched_in  = !empty($emp['sched_in'])  ? $emp['sched_in']  : null;
    $sched_out = !empty($emp['sched_out']) ? $emp['sched_out'] : null;

    /* ---- BASIC SALARY: fixed half-month ---- */

    $earn['BASIC SALARY'] = $monthly / 2;

    /* ---- holidays in the period (date => lowercase type) ---- */

    $holiday_dates = array();

    $hq = $conn->query("SELECT holiday_date, type
                        FROM holidays
                        WHERE holiday_date BETWEEN '$from' AND '$to'");

    if($hq){
        while($h = $hq->fetch_assoc()){
            $holiday_dates[$h['holiday_date']] = strtolower(trim($h['type']));
        }
    }

    /* ---- attendance: presence, late, undertime, holiday premium ---- */

    $present_dates     = array();
    $late_minutes      = 0;
    $undertime_minutes = 0;

    $aq = $conn->query("SELECT *
                        FROM attendance
                        WHERE employee_id = '$empid'
                        AND date BETWEEN '$from' AND '$to'");

    if($aq){
        while($a = $aq->fetch_assoc()){

            $hrs = $a['num_hr'];
            $d   = $a['date'];

            if($hrs <= 0){
                continue;
            }

            $present_dates[$d] = true;

            /* late = time_in after the scheduled start */
            if($sched_in && !empty($a['time_in'])){
                $diff = strtotime($a['time_in']) - strtotime($sched_in);
                if($diff > 0){ $late_minutes += $diff / 60; }
            }

            /* undertime = time_out before the scheduled end */
            if($sched_out && !empty($a['time_out'])){
                $diff = strtotime($sched_out) - strtotime($a['time_out']);
                if($diff > 0){ $undertime_minutes += $diff / 60; }
            }

            /* premium when the present day is a holiday */
            if(isset($holiday_dates[$d])){
                $t = $holiday_dates[$d];

                if($t == 'regular' || $t == 'regular holiday'){
                    $earn['REGULAR HOLIDAY']        += ($hrs * $hourly) * 2;
                    $earn_hours['REGULAR HOLIDAY']  += $hrs;
                }
                else if($t == 'special' || $t == 'special holiday'){
                    $earn['SPECIAL HOLIDAY']        += ($hrs * $hourly) * 0.30;
                    $earn_hours['SPECIAL HOLIDAY']  += $hrs;
                }
            }
        }
    }

    /* regular holiday not worked is still paid one daily rate */
    foreach($holiday_dates as $hd => $t){
        if(!isset($present_dates[$hd]) && ($t == 'regular' || $t == 'regular holiday')){
            $earn['REGULAR HOLIDAY'] += $daily;
        }
    }

    /* ---- ABSENCES = expected workdays - present workdays ----
       expected workdays = calendar days in period, excluding Sundays
       (rest day) and paid holidays. */

    $expected = 0;
    $cur = strtotime($from);
    $end = strtotime($to);

    while($cur <= $end){
        $dstr = date('Y-m-d', $cur);
        $dow  = date('w', $cur); // 0 = Sunday
        if($dow != $rest_day && !isset($holiday_dates[$dstr])){
            $expected++;
        }
        $cur = strtotime('+1 day', $cur);
    }

    $present_work = 0;
    foreach($present_dates as $pd => $x){
        $dow = date('w', strtotime($pd));
        if($dow != $rest_day && !isset($holiday_dates[$pd])){
            $present_work++;
        }
    }

    $absences = $expected - $present_work;
    if($absences < 0){ $absences = 0; }

    $ded['ABSENCES']       = $absences * $daily;
    $ded_count['ABSENCES'] = $absences;

    /* ---- LATE / UNDERTIME (rounded to whole minutes) ---- */

    $late_minutes      = round($late_minutes);
    $undertime_minutes = round($undertime_minutes);

    $ded['LATE']            = $late_minutes * $minute;
    $ded_count['LATE']      = $late_minutes;
    $ded['UNDERTIME']       = $undertime_minutes * $minute;
    $ded_count['UNDERTIME'] = $undertime_minutes;

    /* ---- OVERTIME earnings (existing overtime table) ---- */

    $otq = $conn->query("SELECT SUM(hours) AS h, SUM(hours * rate) AS amt
                         FROM overtime
                         WHERE employee_id = '$empid'
                         AND date_overtime BETWEEN '$from' AND '$to'");

    if($otq && ($otr = $otq->fetch_assoc())){
        $earn['OVERTIME PAY (OT)']       = $otr['amt'] ? $otr['amt'] : 0;
        $earn_hours['OVERTIME PAY (OT)'] = $otr['h']   ? $otr['h']   : 0;
    }

    /* ---- WITHHOLDING TAX on half the monthly salary ---- */

    $ded['WITHHOLDING TAX'] = compute_tax($monthly / 2);

    /* ---- Government contributions (existing data, halved = semi-monthly) ----
       Maps the description in `deductions` to the matching form row. */

    $govmap = array(
        'sss'        => 'SSS CONTRIBUTION',
        'philhealth' => 'PHIC CONTRIBUTION',
        'phic'       => 'PHIC CONTRIBUTION',
        'pagibig'    => 'PAG-IBIG CONTRIBUTION',
        'pag-ibig'   => 'PAG-IBIG CONTRIBUTION',
    );

    $dq = $conn->query("SELECT deductions.description,
                               deductions.amount,
                               deductions.type
                        FROM employee_deductions
                        LEFT JOIN deductions
                          ON deductions.id = employee_deductions.deduction_id
                        WHERE employee_deductions.employee_id = '$empid'
                        AND deductions.is_government = 1");

    if($dq){
        while($g = $dq->fetch_assoc()){

            $desc = strtolower(trim($g['description']));
            $type = strtolower(trim($g['type']));
            $amt  = $g['amount'];

            $val = ($type == 'percent' || $type == 'percentage')
                 ? $monthly * ($amt / 100)
                 : $amt;

            $val = $val / 2;

            if(isset($govmap[$desc]) && isset($ded[$govmap[$desc]])){
                $ded[$govmap[$desc]] += $val;
            }
        }
    }

    /* ---- Department / other deductions for this period ----
       employee_deductions rows whose description matches a form label
       (e.g. Cafeteria, Pharmacy, Laboratory, ...). */

    /* Include rows dated within the period AND undated/recurring rows
       (created_on '0000-00-00' or NULL) so a standing Cafeteria/Pharmacy/etc.
       deduction is applied every cut-off instead of silently disappearing. */
    $edq = $conn->query("SELECT description, amount
                         FROM employee_deductions
                         WHERE employee_id = '$empid'
                         AND amount > 0
                         AND (
                              (created_on BETWEEN '$from' AND '$to')
                              OR created_on = '0000-00-00'
                              OR created_on IS NULL
                         )");

    if($edq){
        while($e = $edq->fetch_assoc()){
            $label = strtoupper(trim($e['description']));
            if($label !== '' && isset($ded[$label])){
                $ded[$label] += $e['amount'];
            }
        }
    }

    /* ---- CASH ADVANCE ---- */

    $caq = $conn->query("SELECT SUM(amount) AS amt
                         FROM cashadvance
                         WHERE employee_id = '$empid'
                         AND date_advance BETWEEN '$from' AND '$to'");

    if($caq && ($car = $caq->fetch_assoc())){
        $ded['CASH ADVANCE'] = $car['amt'] ? $car['amt'] : 0;
    }

    /* ---- totals ---- */

    $te = 0; foreach($earn as $v){ $te += $v; }
    $td = 0; foreach($ded  as $v){ $td += $v; }

    $data['earn']             = $earn;
    $data['earn_hours']       = $earn_hours;
    $data['ded']              = $ded;
    $data['ded_count']        = $ded_count;
    $data['total_earnings']   = $te;
    $data['total_deductions'] = $td;
    $data['net']              = $te - $td;

    return $data;
}
?>

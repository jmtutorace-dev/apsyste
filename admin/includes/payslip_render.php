<?php
/*
|--------------------------------------------------------------------------
| SHARED PAYSLIP RENDERER
|--------------------------------------------------------------------------
|
| render_payslip_html($p, $period_title) returns the ACE Butuan payslip form
| (HTML for TCPDF) for ONE employee, from the array returned by
| compute_payslip(). Used by both admin/payslip_generate.php and the employee
| self-service portal so the printed form is identical everywhere.
|
| Requires payslip_compute.php to have been included first (for the
| $PAYSLIP_EARNING_ROWS / $PAYSLIP_DEDUCTION_ROWS label lists).
|
*/

if(!function_exists('payslip_period_title')){
    /* "MAY 16-31, 2026" when same month/year, otherwise a full span.
       $a / $b are the raw "MM/DD/YYYY" strings from the daterangepicker. */
    function payslip_period_title($a, $b){
        if(date('Y-m', strtotime($a)) == date('Y-m', strtotime($b))){
            return strtoupper(date('M', strtotime($a)))
                 .' '.date('j', strtotime($a))
                 .'-'.date('j', strtotime($b))
                 .', '.date('Y', strtotime($b));
        }
        return strtoupper(date('M j, Y', strtotime($a)))
             .' - '.strtoupper(date('M j, Y', strtotime($b)));
    }
}

if(!function_exists('ps_amt')){
    function ps_amt($v){
        return ($v > 0.0001) ? number_format($v, 2) : '-';
    }
}

if(!function_exists('ps_count')){
    function ps_count($v){
        return ($v > 0) ? number_format($v, 2) : '-';
    }
}

if(!function_exists('render_payslip_html')){
function render_payslip_html($p, $period_title, $company_name = 'ALLIED CARE EXPERTS (ACE) MEDICAL CENTER - BUTUAN, INC.'){

    global $PAYSLIP_EARNING_ROWS, $PAYSLIP_DEDUCTION_ROWS;

    $emp = $p['employee'];

    $name = trim($emp['firstname'].' '.$emp['lastname']);

    $maxrows = max(count($PAYSLIP_EARNING_ROWS), count($PAYSLIP_DEDUCTION_ROWS));

    $rows = '';

    for($i = 0; $i < $maxrows; $i++){

        if(isset($PAYSLIP_EARNING_ROWS[$i])){
            $el = $PAYSLIP_EARNING_ROWS[$i];
            $e_label = $el;
            $e_hours = ps_count($p['earn_hours'][$el]);
            $e_amt   = ps_amt($p['earn'][$el]);
        }else{
            $e_label = '&nbsp;'; $e_hours = ''; $e_amt = '';
        }

        if(isset($PAYSLIP_DEDUCTION_ROWS[$i])){
            $dl = $PAYSLIP_DEDUCTION_ROWS[$i];
            $d_label = $dl;
            $d_count = ps_count($p['ded_count'][$dl]);
            $d_amt   = ps_amt($p['ded'][$dl]);
        }else{
            $d_label = '&nbsp;'; $d_count = ''; $d_amt = '';
        }

        $rows .= '
        <tr>
            <td width="20%" style="border-right:0.5px solid #ccc;">'.$e_label.'</td>
            <td width="7%"  align="center" style="border-right:0.5px solid #ccc;">'.$e_hours.'</td>
            <td width="13%" align="right"  style="border-right:1px solid #888;">'.$e_amt.'</td>
            <td width="20%" style="border-right:0.5px solid #ccc;">'.$d_label.'</td>
            <td width="7%"  align="center" style="border-right:0.5px solid #ccc;">'.$d_count.'</td>
            <td width="13%" align="right">'.$d_amt.'</td>
        </tr>';
    }

    return '
    <table cellpadding="3" cellspacing="0" style="width:100%; border:1px solid #000;">

        <tr>
            <td colspan="6" align="center" style="font-size:12px; font-weight:bold; border-bottom:1px solid #000;">
                '.htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8').'
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center" style="font-size:10px; font-weight:bold; border-bottom:1px solid #000;">
                '.$period_title.'
            </td>
        </tr>

        <tr>
            <td colspan="3" style="font-size:9px; border-right:1px solid #888; border-bottom:1px solid #000;">
                <b>EMPLOYEE:</b> '.$name.'
            </td>
            <td colspan="3" style="font-size:9px; border-bottom:1px solid #000;">
                <b>EMPLOYEE ID:</b> '.$emp['employee_id'].'
            </td>
        </tr>

        <tr style="background-color:#e6e6e6; font-weight:bold; font-size:8px;">
            <td width="20%" style="border-right:0.5px solid #ccc; border-bottom:1px solid #000;">EARNINGS</td>
            <td width="7%"  align="center" style="border-right:0.5px solid #ccc; border-bottom:1px solid #000;">HOURS</td>
            <td width="13%" align="right"  style="border-right:1px solid #888; border-bottom:1px solid #000;">AMOUNT</td>
            <td width="20%" style="border-right:0.5px solid #ccc; border-bottom:1px solid #000;">DEDUCTIONS</td>
            <td width="7%"  align="center" style="border-right:0.5px solid #ccc; border-bottom:1px solid #000;">DAY/MIN</td>
            <td width="13%" align="right"  style="border-bottom:1px solid #000;">AMOUNT</td>
        </tr>
        '.$rows.'

        <tr style="font-weight:bold; font-size:8px; background-color:#f2f2f2;">
            <td colspan="2" style="border-top:1px solid #000; border-right:0.5px solid #ccc;">TOTAL EARNINGS</td>
            <td align="right" style="border-top:1px solid #000; border-right:1px solid #888;">'.ps_amt($p['total_earnings']).'</td>
            <td colspan="2" style="border-top:1px solid #000; border-right:0.5px solid #ccc;">TOTAL DEDUCTIONS</td>
            <td align="right" style="border-top:1px solid #000;">'.ps_amt($p['total_deductions']).'</td>
        </tr>

        <tr style="font-weight:bold; font-size:10px;">
            <td colspan="4" align="right" style="border-top:1px solid #000;">NET PAY</td>
            <td colspan="2" align="right" style="border-top:1px solid #000;">'.number_format($p['net'], 2).'</td>
        </tr>

    </table>';
}
}
?>

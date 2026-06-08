<?php
/*
 Single source for payroll/company settings (one row in payroll_settings).
 get_payroll_settings($conn) returns an associative array with sensible
 defaults so callers never have to null-check.
*/

if(!function_exists('get_payroll_settings')){
function get_payroll_settings($conn){

    static $cache = null;
    if($cache !== null){ return $cache; }

    $defaults = array(
        'company_name'        => 'ALLIED CARE EXPERTS (ACE) MEDICAL CENTER - BUTUAN, INC.',
        'company_address'     => '',
        'rest_day'            => 0,    // 0 = Sunday .. 6 = Saturday
        'working_days'        => 26,   // working days per month (daily rate basis)
        'thirteenth_mode'     => 'full',
        'midyear_percentage'  => 50,
    );

    $res = @$conn->query("SELECT * FROM payroll_settings LIMIT 1");
    if($res && $res->num_rows){
        $row = $res->fetch_assoc();
        foreach($defaults as $k => $v){
            if(isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null){
                $defaults[$k] = $row[$k];
            }
        }
    }

    $cache = $defaults;
    return $cache;
}
}
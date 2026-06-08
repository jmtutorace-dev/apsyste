<?php

if(!function_exists('compute_tax')){

function compute_tax($salary) {

    $tax = 0;

    if ($salary <= 10417) {
        $tax = 0;

    } elseif ($salary <= 16666) {
        $tax = ($salary - 10417) * 0.15;

    } elseif ($salary <= 33332) {
        $tax = 937.50 + ($salary - 16667) * 0.20;

    } elseif ($salary <= 83332) {
        $tax = 4270.70 + ($salary - 33333) * 0.25;

    } elseif ($salary <= 333332) {
        $tax = 16770.70 + ($salary - 83333) * 0.30;

    } else {
        $tax = 91770.70 + ($salary - 333333) * 0.35;
    }

    return round($tax, 2);
}

} // function_exists guard
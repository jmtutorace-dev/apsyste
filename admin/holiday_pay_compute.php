<?php

function computeHolidayPay(
    $employee_id,
    $date,
    $worked_hours,
    $daily_rate,
    $hourly_rate,
    $conn
){

    $holiday_pay = 0;

    // =====================================================
    // CHECK HOLIDAY
    // =====================================================

    $hsql = "SELECT *
             FROM holidays
             WHERE holiday_date = '$date'";

    $hquery = $conn->query($hsql);

    if($hquery->num_rows > 0){

        $holiday = $hquery->fetch_assoc();

        $type = strtolower($holiday['type']);

        // =================================================
        // REGULAR HOLIDAY
        // =================================================

        if($type == 'regular'){

            // worked
            if($worked_hours > 0){

                // 200%
                $holiday_pay =
                    ($hourly_rate * $worked_hours) * 2;
            }

            // no work
            else{

                // 100%
                $holiday_pay = $daily_rate;
            }
        }

        // =================================================
        // SPECIAL HOLIDAY
        // =================================================

        elseif($type == 'special'){

            // worked only
            if($worked_hours > 0){

                // 130%
                $holiday_pay =
                    ($hourly_rate * $worked_hours) * 1.30;
            }
        }
    }

    return $holiday_pay;
}

?>
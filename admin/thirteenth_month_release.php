<?php
include 'includes/session.php';

if(!isset($_GET['id']) || !isset($_GET['type'])){
    $_SESSION['error'] = 'Invalid request';
    header('location: thirteenth_month.php');
    exit();
}

$empid = intval($_GET['id']);
$type = trim($_GET['type']);

if($type != 'Midyear' && $type != 'Year End'){
    $_SESSION['error'] = 'Invalid release type';
    header('location: thirteenth_month.php');
    exit();
}

$currentYear = date('Y');

/*
|--------------------------------------------------------------------------
| GET EMPLOYEE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        employees.*,
        position.rate
    FROM employees
    LEFT JOIN position
    ON position.id = employees.position_id
    WHERE employees.id = '$empid'
";

$query = $conn->query($sql);

if($query->num_rows == 0){

    $_SESSION['error'] = 'Employee not found';
    header('location: thirteenth_month.php');
    exit();
}

$employee = $query->fetch_assoc();

$monthlySalary =
    !empty($employee['rate'])
    ? $employee['rate']
    : 0;

/*
|--------------------------------------------------------------------------
| COMPUTE MONTHS WORKED
|--------------------------------------------------------------------------
*/

$dateHired = $employee['created_on'];

$hireYear = date(
    'Y',
    strtotime($dateHired)
);

if($hireYear < $currentYear){

    $monthsWorked = 12;
}
else{

    $hireMonth = date(
        'n',
        strtotime($dateHired)
    );

    $monthsWorked =
        (12 - $hireMonth) + 1;

    if($monthsWorked < 0){
        $monthsWorked = 0;
    }
}

/*
|--------------------------------------------------------------------------
| COMPUTE 13TH MONTH
|--------------------------------------------------------------------------
*/

$totalBasicSalaryEarned =
    $monthlySalary *
    $monthsWorked;

$thirteenthMonth =
    $totalBasicSalaryEarned / 12;

/*
|--------------------------------------------------------------------------
| SETTINGS
|--------------------------------------------------------------------------
*/

$mode = 'split';
$midyearPercent = 50;

$setsql = "
    SELECT *
    FROM payroll_settings
";

$setquery = $conn->query($setsql);

while($setrow = $setquery->fetch_assoc()){

    if($setrow['setting_name'] == 'thirteenth_month_mode'){
        $mode = $setrow['setting_value'];
    }

    if($setrow['setting_name'] == 'midyear_percentage'){
        $midyearPercent = $setrow['setting_value'];
    }
}

/*
|--------------------------------------------------------------------------
| CHECK RELEASED AMOUNT THIS YEAR
|--------------------------------------------------------------------------
*/

$relsql = "
    SELECT
        SUM(amount) AS total_released
    FROM thirteenth_month_release
    WHERE employee_id = '$empid'
    AND release_year = '$currentYear'
";

$relquery = $conn->query($relsql);

$relrow = $relquery->fetch_assoc();

$totalReleased =
    !empty($relrow['total_released'])
    ? $relrow['total_released']
    : 0;

/*
|--------------------------------------------------------------------------
| PREVENT DUPLICATE RELEASE
|--------------------------------------------------------------------------
*/

$dupsql = "
    SELECT id
    FROM thirteenth_month_release
    WHERE employee_id = '$empid'
    AND release_year = '$currentYear'
    AND release_type = '$type'
";

$dupquery = $conn->query($dupsql);

if($dupquery->num_rows > 0){

    $_SESSION['error'] =
        $type.' already released for '.$currentYear;

    header('location: thirteenth_month.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| COMPUTE RELEASE AMOUNT
|--------------------------------------------------------------------------
*/

$amount = 0;

if($mode == 'split'){

    if($type == 'Midyear'){

        $amount =
            $thirteenthMonth *
            ($midyearPercent / 100);
    }
    else{

        $amount =
            $thirteenthMonth -
            $totalReleased;
    }
}
else{

    if($type == 'Midyear'){

        $_SESSION['error'] =
            'Midyear release disabled';

        header('location: thirteenth_month.php');
        exit();
    }

    $amount =
        $thirteenthMonth -
        $totalReleased;
}

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if($amount <= 0){

    $_SESSION['error'] =
        'No remaining 13th month balance';

    header('location: thirteenth_month.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| INSERT RELEASE
|--------------------------------------------------------------------------
*/

$insql = "
    INSERT INTO thirteenth_month_release
    (
        employee_id,
        release_year,
        release_date,
        amount,
        release_type
    )
    VALUES
    (
        '$empid',
        '$currentYear',
        CURDATE(),
        '$amount',
        '$type'
    )
";

if($conn->query($insql)){

    $_SESSION['success'] =
        '13th Month '.$type.
        ' released successfully. Amount: ₱'.
        number_format($amount,2);
}
else{

    $_SESSION['error'] =
        $conn->error;
}

header('location: thirteenth_month.php');
exit();
?>
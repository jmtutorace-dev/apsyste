<?php

include 'includes/session.php';

if(isset($_POST['add'])){

    $employee_id = $_POST['employee_id'];
    $deduction_id = $_POST['deduction_id'];
    $amount = $_POST['amount'];
    $created_on = $_POST['created_on'];

    $deduction = $conn->query("
        SELECT *
        FROM deduction_types
        WHERE id = '$deduction_id'
    ");

    if($deduction->num_rows > 0){

        $drow = $deduction->fetch_assoc();

        $description = $drow['deduction_name'];

        $sql = "
            INSERT INTO employee_deductions
            (
                employee_id,
                deduction_id,
                description,
                amount,
                created_on
            )
            VALUES
            (
                '$employee_id',
                '$deduction_id',
                '$description',
                '$amount',
                '$created_on'
            )
        ";

        if($conn->query($sql)){

            $_SESSION['success'] =
                'Employee deduction added successfully';

        }
        else{

            $_SESSION['error'] =
                $conn->error;
        }
    }
    else{

        $_SESSION['error'] =
            'Invalid deduction type selected';
    }
}
else{

    $_SESSION['error'] =
        'Fill up add deduction form first';
}

header('location: employee_deductions.php');

?>
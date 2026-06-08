<?php
include 'includes/session.php';

if(isset($_POST['add'])){

    $employee_id  = intval($_POST['employee_id']);
    $deduction_id = intval($_POST['deduction_id']);
    $amount       = $_POST['amount'];
    $created_on   = $_POST['created_on'];

    $lookup = $conn->prepare("SELECT deduction_name FROM deduction_types WHERE id = ?");
    $lookup->bind_param('i', $deduction_id);
    $lookup->execute();
    $res = $lookup->get_result();

    if($res->num_rows > 0){

        $description = $res->fetch_assoc()['deduction_name'];

        $stmt = $conn->prepare("INSERT INTO employee_deductions (employee_id, deduction_id, description, amount, created_on) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisds', $employee_id, $deduction_id, $description, $amount, $created_on);
        if($stmt->execute()){
            $_SESSION['success'] = 'Employee deduction added successfully';
        }else{
            $_SESSION['error'] = 'Operation failed. Please try again.';
        }
    }
    else{
        $_SESSION['error'] = 'Invalid deduction type selected';
    }
}
else{
    $_SESSION['error'] = 'Fill up add deduction form first';
}

header('location: employee_deductions.php');
exit();
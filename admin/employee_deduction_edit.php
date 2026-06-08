<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

    $id     = intval($_POST['id']);
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("UPDATE employee_deductions SET amount = ? WHERE id = ?");
    $stmt->bind_param('di', $amount, $id);

    if($stmt->execute()){
        if($stmt->affected_rows > 0){
            $_SESSION['success'] = 'Employee deduction updated successfully';
        }else{
            $_SESSION['error'] = 'No changes made';
        }
    }else{
        $_SESSION['error'] = 'Operation failed. Please try again.';
    }
}
else{
    $_SESSION['error'] = 'Fill up edit deduction form first';
}

header('location: employee_deductions.php');
exit();
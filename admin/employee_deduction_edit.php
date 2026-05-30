<?php

include 'includes/session.php';

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $amount = $_POST['amount'];

    $sql = "
        UPDATE employee_deductions
        SET amount = '$amount'
        WHERE id = '$id'
    ";

    if($conn->query($sql)){

        if($conn->affected_rows > 0){

            $_SESSION['success'] =
                'Employee deduction updated successfully';

        }
        else{

            $_SESSION['error'] =
                'No changes made';
        }

    }
    else{

        $_SESSION['error'] =
            $conn->error;
    }

}
else{

    $_SESSION['error'] =
        'Fill up edit deduction form first';
}

header('location: employee_deductions.php');
exit();

?>
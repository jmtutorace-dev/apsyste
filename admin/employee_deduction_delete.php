<?php

include 'includes/session.php';

if(isset($_POST['delete'])){

    $id = intval($_POST['id']);

    $sql = "
        DELETE FROM employee_deductions
        WHERE id = '$id'
    ";

    if($conn->query($sql)){

        if($conn->affected_rows > 0){

            $_SESSION['success'] =
                'Employee deduction deleted successfully';

        }
        else{

            $_SESSION['error'] =
                'Deduction record not found';
        }

    }
    else{

        $_SESSION['error'] =
            $conn->error;
    }

}
else{

    $_SESSION['error'] =
        'Select deduction to delete first';
}

header('location: employee_deductions.php');

?>
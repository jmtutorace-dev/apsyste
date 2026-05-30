<?php

include 'includes/session.php';

if(isset($_POST['edit'])){

    $id = $_POST['id'];

    $deduction_name = trim($_POST['deduction_name']);

    $description = trim($_POST['description']);

    $sql = "
        UPDATE deduction_types
        SET
            deduction_name = '$deduction_name',
            description = '$description'
        WHERE id = '$id'
    ";

    if($conn->query($sql)){

        $_SESSION['success'] =
            'Deduction type updated successfully';

    }
    else{

        $_SESSION['error'] =
            $conn->error;
    }

}
else{

    $_SESSION['error'] =
        'Fill up edit form first';
}

header('location: deduction_types.php');

?>
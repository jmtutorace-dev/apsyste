<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

    $id             = intval($_POST['id']);
    $deduction_name = trim($_POST['deduction_name']);
    $description    = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE deduction_types SET deduction_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param('ssi', $deduction_name, $description, $id);
    if($stmt->execute()){
        $_SESSION['success'] = 'Deduction type updated successfully';
    }else{
        $_SESSION['error'] = 'Operation failed. Please try again.';
    }
}
else{
    $_SESSION['error'] = 'Fill up edit form first';
}

header('location: deduction_types.php');
exit();
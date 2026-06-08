<?php
include 'includes/session.php';

if(isset($_POST['add'])){

    $holiday_date = trim($_POST['holiday_date']);
    $description  = trim($_POST['description']);
    $type         = trim($_POST['type']);

    $stmt = $conn->prepare("INSERT INTO holidays (holiday_date, description, type) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $holiday_date, $description, $type);

    if($stmt->execute()){
        $_SESSION['success'] = 'Holiday added successfully';
    }else{
        $_SESSION['error'] = 'Operation failed. Please try again.';
    }
}

header('location: holidays.php');
exit();
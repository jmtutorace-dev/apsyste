<?php
include 'includes/session.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    $_SESSION['error'] = 'Invalid employee.';
    header('location: employee.php');
    exit();
}

// Reset the employee's portal password back to the default
$hash = password_hash('123456', PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE employees SET password = ? WHERE id = ?");
$stmt->bind_param('si', $hash, $id);

if($stmt->execute()){
    $_SESSION['success'] = 'Portal password reset to the default (123456). Ask the employee to change it after logging in.';
}else{
    $_SESSION['error'] = 'Could not reset the password. Please try again.';
}

header('location: employee.php');
exit();
<?php
include 'includes/session.php';   // loads $emp (incl. current password hash)

if(isset($_POST['change_password'])){

    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if(!password_verify($current, $emp['password'])){
        $_SESSION['error'] = 'Your current password is incorrect.';
    }
    elseif(strlen($new) < 6){
        $_SESSION['error'] = 'New password must be at least 6 characters.';
    }
    elseif($new !== $confirm){
        $_SESSION['error'] = 'New password and confirmation do not match.';
    }
    else{
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE employees SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hash, $emp['id']);

        if($stmt->execute()){
            $_SESSION['success'] = 'Your password has been updated.';
        }else{
            $_SESSION['error'] = 'Could not update password. Please try again.';
        }
    }
}

header('location: account.php');
exit();
?>
<?php
include 'includes/session.php';

if(isset($_POST['add'])){

    $deduction_name = trim($_POST['deduction_name']);
    $description    = trim($_POST['description']);

    if(empty($deduction_name)){
        $_SESSION['error'] = 'Deduction name is required';
    }
    else{
        $chk = $conn->prepare("SELECT id FROM deduction_types WHERE deduction_name = ?");
        $chk->bind_param('s', $deduction_name);
        $chk->execute();

        if($chk->get_result()->num_rows > 0){
            $_SESSION['error'] = 'Deduction type already exists';
        }
        else{
            $stmt = $conn->prepare("INSERT INTO deduction_types (deduction_name, description) VALUES (?, ?)");
            $stmt->bind_param('ss', $deduction_name, $description);
            if($stmt->execute()){
                $_SESSION['success'] = 'Deduction type added successfully';
            }else{
                $_SESSION['error'] = 'Operation failed. Please try again.';
            }
        }
    }
}
else{
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: deduction_types.php');
exit();

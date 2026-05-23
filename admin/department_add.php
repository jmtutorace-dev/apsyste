<?php
include 'includes/session.php';

if(isset($_POST['add'])){

  $name = trim($_POST['name']);

  if(empty($name)){
    $_SESSION['error'] = "Department name is required";
  }
  else{

    // SAFE INSERT
    $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if($stmt->execute()){
      $_SESSION['success'] = "Department added successfully";
    }
    else{
      $_SESSION['error'] = $conn->error;
    }
  }

}
else{
  $_SESSION['error'] = "Invalid request";
}

header('location: department.php');
?>
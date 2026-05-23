<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

  $id = $_POST['id'];
  $name = $_POST['name'];

  $stmt = $conn->prepare("UPDATE departments SET name=? WHERE id=?");
  $stmt->bind_param("si", $name, $id);

  if($stmt->execute()){
    $_SESSION['success'] = "Department updated successfully";
  }
  else{
    $_SESSION['error'] = $conn->error;
  }

}
else{
  $_SESSION['error'] = "Fill up edit form first";
}

header('location: department.php');
?>
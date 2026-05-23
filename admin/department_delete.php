<?php
include 'includes/session.php';

if(isset($_POST['delete'])){

  $id = $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM departments WHERE id=?");
  $stmt->bind_param("i", $id);

  if($stmt->execute()){
    $_SESSION['success'] = "Department deleted successfully";
  }
  else{
    $_SESSION['error'] = $conn->error;
  }

}
else{
  $_SESSION['error'] = "Select department first";
}

header('location: department.php');
?>
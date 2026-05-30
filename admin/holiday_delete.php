<?php

include 'includes/session.php';

$id = $_GET['id'];

$sql = "DELETE FROM holidays WHERE id='$id'";

if($conn->query($sql)){

    $_SESSION['success'] = 'Holiday deleted successfully';
}
else{

    $_SESSION['error'] = $conn->error;
}

header('location: holidays.php');

?>
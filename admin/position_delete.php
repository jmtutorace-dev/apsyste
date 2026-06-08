<?php
include 'includes/session.php';

if(isset($_POST['delete'])){

    $id = intval($_POST['id']);

    // GET POSITION NAME FIRST
    $sql = "SELECT description FROM position WHERE id='$id'";
    $query = $conn->query($sql);
    $row = $query->fetch_assoc();

    $title = $row['description'];

    // DELETE RECORD
    $sql = "DELETE FROM position WHERE id='$id'";

    if($conn->query($sql)){
        $_SESSION['success'] = 'Salary deleted successfully';
    }
    else{
        $_SESSION['error'] = $conn->error;
    }

    header('location: position_packages.php?position='.urlencode($title));
}
else{
    $_SESSION['error'] = 'Select item to delete first';
    header('location: position.php');
}
?>
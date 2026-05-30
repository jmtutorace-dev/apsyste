<?php

include 'includes/session.php';

if(isset($_POST['add'])){

    $holiday_date = $_POST['holiday_date'];
    $description = $_POST['description'];
    $type = $_POST['type'];

    $sql = "INSERT INTO holidays
            (holiday_date, description, type)

            VALUES

            ('$holiday_date', '$description', '$type')";

    if($conn->query($sql)){

        $_SESSION['success'] = 'Holiday added successfully';
    }
    else{

        $_SESSION['error'] = $conn->error;
    }
}

header('location: holidays.php');

?>
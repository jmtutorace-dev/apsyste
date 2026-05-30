<?php

include 'includes/session.php';

$response = array();

if(isset($_POST['id'])){

    $id = $_POST['id'];

    $sql = "
        SELECT *
        FROM deduction_types
        WHERE id = '$id'
    ";

    $query = $conn->query($sql);

    if($query->num_rows > 0){

        $row = $query->fetch_assoc();

        $response = array(
            'id' => $row['id'],
            'deduction_name' => $row['deduction_name'],
            'description' => $row['description']
        );
    }
}

echo json_encode($response);

?>
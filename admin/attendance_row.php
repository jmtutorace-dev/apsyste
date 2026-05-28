<?php
include 'includes/session.php';

if(isset($_POST['id'])){

    $id = $_POST['id'];

    $sql = "SELECT attendance.*,
                   attendance.id AS attid,
                   employees.firstname,
                   employees.lastname
            FROM attendance
            LEFT JOIN employees
            ON employees.id = attendance.employee_id
            WHERE attendance.id = '$id'";

    $query = $conn->query($sql);

    $row = $query->fetch_assoc();

    echo json_encode($row);
}
?>
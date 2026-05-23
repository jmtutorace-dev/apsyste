<?php 
include 'includes/session.php';

if(isset($_POST['id'])){

  $id = $_POST['id'];

  $sql = "SELECT 
            employees.*,
            employees.id AS empid,
            position.id AS position_id,
            position.description AS position_name,
            schedules.time_in,
            schedules.time_out
          FROM employees
          LEFT JOIN position ON position.id = employees.position_id
          LEFT JOIN schedules ON schedules.id = employees.schedule_id
          WHERE employees.id = '$id'";

  $query = $conn->query($sql);
  $row = $query->fetch_assoc();

  echo json_encode($row);
}
?>
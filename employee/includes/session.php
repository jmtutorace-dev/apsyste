<?php
session_start();

// project-root conn.php (employee/includes -> out two levels)
include __DIR__ . '/../../conn.php';

// Gatekeeper: must be a logged-in employee
if(!isset($_SESSION['employee']) || trim($_SESSION['employee']) == ''){
    header('location: index.php');
    exit();
}

$eid = intval($_SESSION['employee']);

$sql = "SELECT employees.*,
               position.rate,
               position.description AS position_name,
               schedules.time_in  AS sched_in,
               schedules.time_out AS sched_out
        FROM employees
        LEFT JOIN position  ON position.id  = employees.position_id
        LEFT JOIN schedules ON schedules.id = employees.schedule_id
        WHERE employees.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $eid);
$stmt->execute();
$emp = $stmt->get_result()->fetch_assoc();

if(!$emp){
    session_unset();
    session_destroy();
    header('location: index.php');
    exit();
}
?>

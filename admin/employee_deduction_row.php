<?php

include 'includes/session.php';

$id = intval($_POST['id']);

$sql = "
    SELECT
        ed.*,
        e.firstname,
        e.lastname,
        dt.deduction_name
    FROM employee_deductions ed
    INNER JOIN employees e
        ON e.id = ed.employee_id
    INNER JOIN deduction_types dt
        ON dt.id = ed.deduction_id
    WHERE ed.id = '$id'
";

$query = $conn->query($sql);

$row = $query->fetch_assoc();

echo json_encode($row);

?>
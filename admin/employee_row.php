<?php
include 'includes/session.php';

header('Content-Type: application/json');

$response = array();

if(isset($_POST['id'])){

    $id = intval($_POST['id']);

    $sql = "SELECT
                employees.*,
                employees.id AS empid,
                position.description AS position_name,
                schedules.time_in,
                schedules.time_out
            FROM employees
            LEFT JOIN position
                ON position.id = employees.position_id
            LEFT JOIN schedules
                ON schedules.id = employees.schedule_id
            WHERE employees.id = $id";

    $query = $conn->query($sql);

    if(!$query){
        echo json_encode([
            'error' => $conn->error
        ]);
        exit();
    }

    $row = $query->fetch_assoc();

    if(!$row){
        echo json_encode([
            'error' => 'Employee not found'
        ]);
        exit();
    }

    $benefits = array();

    $dsql = "SELECT deductions.description
             FROM employee_deductions
             LEFT JOIN deductions
             ON deductions.id = employee_deductions.deduction_id
             WHERE employee_deductions.employee_id = $id";

    $dquery = $conn->query($dsql);

    if($dquery){
        while($drow = $dquery->fetch_assoc()){
            $benefits[] = $drow['description'];
        }
    }

    $row['benefits'] = implode('<br>', $benefits);

    echo json_encode($row);
    exit();
}

echo json_encode([
    'error' => 'No ID received'
]);
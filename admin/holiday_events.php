<?php

include 'includes/session.php';

$data = array();

$sql = "SELECT * FROM holidays";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

    $color = '#f39c12';

    if(strtolower($row['type']) == 'regular'){
        $color = '#dd4b39';
    }

    $data[] = array(
        'title' => $row['description'].' ('.$row['type'].')',
        'start' => $row['holiday_date'],
        'color' => $color
    );
}

echo json_encode($data);

?>
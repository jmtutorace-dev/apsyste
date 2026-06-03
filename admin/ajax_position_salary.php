<?php

include 'includes/session.php';

if(isset($_POST['position_name'])){

    $position_name = $_POST['position_name'];

    $sql = "
        SELECT *
        FROM position
        WHERE description = '$position_name'
        ORDER BY rate ASC
    ";

    $query = $conn->query($sql);

    echo '<option value="">- Select Salary Package -</option>';

    while($row = $query->fetch_assoc()){

        echo '
            <option value="'.$row['id'].'">
                ₱'.number_format($row['rate'],2).'
            </option>
        ';
    }

}
?>
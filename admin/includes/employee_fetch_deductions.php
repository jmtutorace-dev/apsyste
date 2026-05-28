
<?php
include 'session.php';

if(!isset($_POST['id'])){
    exit();
}

$empid = $_POST['id'];

$selected = array();

// GET EMPLOYEE DEDUCTIONS
$sql_sel = "SELECT deduction_id 
            FROM employee_deductions 
            WHERE employee_id = '$empid'";

$query_sel = $conn->query($sql_sel);

while($row_sel = $query_sel->fetch_assoc()){
    $selected[] = $row_sel['deduction_id'];
}

// GET ALL DEDUCTIONS
$sql = "SELECT * FROM deductions ORDER BY description ASC";
$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

    $checked = in_array($row['id'], $selected) ? 'checked' : '';

    echo "
        <div class='checkbox'>
            <label>
                <input type='checkbox'
                       name='deductions[]'
                       value='".$row['id']."'
                       ".$checked.">

                ".$row['description']." 
                (".$row['amount']." ".$row['type'].")
            </label>
        </div>
    ";
}
?>


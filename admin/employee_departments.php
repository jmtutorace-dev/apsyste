<?php
include 'includes/session.php';

$sql = "SELECT DISTINCT department FROM employees ORDER BY department ASC";
$query = $conn->query($sql);

while($row = $query->fetch_assoc()){
  echo "<option value='".$row['department']."'>".$row['department']."</option>";
}
?>
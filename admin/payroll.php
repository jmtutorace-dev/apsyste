<?php include 'includes/session.php'; ?>
<?php
  include '../timezone.php';
  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('-30 day', strtotime($range_to)));
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
  <h1>Payroll</h1>
</section>

<section class="content">

<?php
if(isset($_SESSION['error'])){
  echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
  unset($_SESSION['error']);
}
if(isset($_SESSION['success'])){
  echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
  unset($_SESSION['success']);
}
?>

<div class="box">
<div class="box-body">

<table id="example1" class="table table-bordered">

<thead>
  <th>Employee Name</th>
  <th>Employee ID</th>
  <th>Base Pay (15 days)</th>
  <th>Overtime Pay</th>
  <th>Gross</th>
  <th>Deductions</th>
  <th>Cash Advance</th>
  <th>Net Pay</th>
</thead>

<tbody>

<?php

// DATE RANGE
$to = date('Y-m-d');
$from = date('Y-m-d', strtotime('-30 day', strtotime($to)));

if(isset($_GET['range'])){
  $range = $_GET['range'];
  $ex = explode(' - ', $range);
  $from = date('Y-m-d', strtotime($ex[0]));
  $to = date('Y-m-d', strtotime($ex[1]));
}

// EMPLOYEES + ATTENDANCE (kept for overtime tracking)
$sql = "SELECT *,
               SUM(num_hr) AS total_hr,
               attendance.employee_id AS empid
        FROM attendance
        LEFT JOIN employees ON employees.id = attendance.employee_id
        LEFT JOIN position ON position.id = employees.position_id
        WHERE date BETWEEN '$from' AND '$to'
        GROUP BY attendance.employee_id";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

  $empid = $row['empid'];

  // CASH ADVANCE
  $casql = "SELECT SUM(amount) AS cashamount
            FROM cashadvance
            WHERE employee_id = '$empid'
            AND date_advance BETWEEN '$from' AND '$to'";
  $caquery = $conn->query($casql);
  $carow = $caquery->fetch_assoc();
  $cashadvance = $carow['cashamount'] ?? 0;

  // OVERTIME
  $otsql = "SELECT SUM(hours * rate) AS overtime_pay
            FROM overtime
            WHERE employee_id = '$empid'
            AND date_overtime BETWEEN '$from' AND '$to'";
  $otquery = $conn->query($otsql);
  $otrow = $otquery->fetch_assoc();
  $overtime = $otrow['overtime_pay'] ?? 0;

  // =========================
  // FIXED SALARY SYSTEM
  // =========================
  $monthly_salary = $row['rate'];

  // semi-monthly base pay (15 days)
  $base_pay = $monthly_salary / 2;

  // gross pay
  $gross = $base_pay + $overtime;

  // TOTAL DEDUCTIONS (cash advance only OR your tax if you already use it)
  $total_deduction = $cashadvance;

  // net pay
  $net = $gross - $total_deduction;

  echo "
    <tr>
      <td>".$row['lastname'].", ".$row['firstname']."</td>
      <td>".$row['employee_id']."</td>
      <td>".number_format($base_pay, 2)."</td>
      <td>".number_format($overtime, 2)."</td>
      <td>".number_format($gross, 2)."</td>
      <td>0.00</td>
      <td>".number_format($cashadvance, 2)."</td>
      <td><b>".number_format($net, 2)."</b></td>
    </tr>
  ";
}

?>

</tbody>
</table>

</div>
</div>

</section>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
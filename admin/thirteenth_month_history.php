<?php include 'includes/session.php'; ?>
<?php include 'includes/thirteenth_month_compute.php'; ?>
<?php

if(!isset($_GET['id'])){
    $_SESSION['error'] = 'Employee ID missing';
    header('location: thirteenth_month.php');
    exit();
}

$empid = intval($_GET['id']);
$year  = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

$tm = compute_thirteenth_month($conn, $empid, $year);

if(!$tm['found']){
    $_SESSION['error'] = 'Employee not found';
    header('location: thirteenth_month.php?year='.$year);
    exit();
}

$employee = $tm['employee'];

?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
    <h1>13th Month Release History</h1>
</section>

<section class="content">

<?php
if(isset($_SESSION['success'])){
    echo "
    <div class='alert alert-success alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        <h4><i class='icon fa fa-check'></i> Success!</h4>
        ".$_SESSION['success']."
    </div>
    ";
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])){
    echo "
    <div class='alert alert-danger alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        <h4><i class='icon fa fa-warning'></i> Error!</h4>
        ".$_SESSION['error']."
    </div>
    ";
    unset($_SESSION['error']);
}
?>

<!-- EMPLOYEE SUMMARY -->
<div class="box">

<div class="box-header with-border">
    <h3 class="box-title">
        <?php echo htmlspecialchars($employee['lastname'].', '.$employee['firstname'], ENT_QUOTES, 'UTF-8'); ?>
        <small>(<?php echo htmlspecialchars($employee['employee_id'], ENT_QUOTES, 'UTF-8'); ?>)</small>
    </h3>
</div>

<div class="box-body">

<table class="table table-bordered">

<tr>
    <th width="40%">Date Hired</th>
    <td><?php echo date('F d, Y', strtotime($tm['date_hired'])); ?></td>
</tr>

<tr>
    <th>Monthly Salary</th>
    <td>&#8369;<?php echo number_format($tm['monthly_salary'], 2); ?></td>
</tr>

<tr>
    <th>Months Worked (<?php echo $year; ?>)</th>
    <td><?php echo $tm['months_worked']; ?></td>
</tr>

<tr>
    <th>Basic Salary Earned (<?php echo $year; ?>)</th>
    <td>&#8369;<?php echo number_format($tm['basic_earned'], 2); ?></td>
</tr>

<tr>
    <th>13th Month Pay (<?php echo $year; ?>)</th>
    <td><b>&#8369;<?php echo number_format($tm['thirteenth_month'], 2); ?></b></td>
</tr>

<tr>
    <th>Total Released (<?php echo $year; ?>)</th>
    <td>&#8369;<?php echo number_format($tm['released'], 2); ?></td>
</tr>

<tr>
    <th>Remaining Balance</th>
    <td><b>&#8369;<?php echo number_format($tm['balance'], 2); ?></b></td>
</tr>

</table>

</div>

</div>

<!-- RELEASE HISTORY -->
<div class="box">

<div class="box-header with-border">
    <h3 class="box-title">Release History</h3>

    <div class="box-tools pull-right">
        <a href="thirteenth_month.php?year=<?php echo $year; ?>" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <a href="thirteenth_month_pdf.php?id=<?php echo $empid; ?>&year=<?php echo $year; ?>"
           class="btn btn-danger btn-sm" target="_blank">
            <i class="fa fa-file-pdf-o"></i> PDF
        </a>
    </div>
</div>

<div class="box-body">

<table id="example1" class="table table-bordered table-striped">

<thead>
<tr>
    <th>Date Released</th>
    <th>Type</th>
    <th>Year</th>
    <th class="text-right">Amount</th>
</tr>
</thead>

<tbody>

<?php

$hsql = "
    SELECT *
    FROM thirteenth_month_release
    WHERE employee_id = '$empid'
    ORDER BY release_date DESC, id DESC
";

$hquery = $conn->query($hsql);

if($hquery){
    while($hrow = $hquery->fetch_assoc()){
        echo "
        <tr>
            <td>".date('M d, Y', strtotime($hrow['release_date']))."</td>
            <td>".$hrow['release_type']."</td>
            <td>".$hrow['release_year']."</td>
            <td class='text-right'>&#8369;".number_format($hrow['amount'], 2)."</td>
        </tr>
        ";
    }
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

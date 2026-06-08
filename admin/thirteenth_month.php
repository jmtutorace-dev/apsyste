<?php include 'includes/session.php'; ?>
<?php include 'includes/thirteenth_month_compute.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
    <h1>13th Month Pay</h1>
</section>

<section class="content">
<?php

/* ---- Selected year ---- */

$currentYear  = date('Y');
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : intval($currentYear);

/* ---- Flash messages ---- */

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

<!-- HOW IT IS COMPUTED -->
<div class="callout callout-info" style="border-radius:10px;">
    <h4><i class="fa fa-info-circle"></i> How this is computed</h4>
    <p style="margin-bottom:0;">
        <b>13th Month Pay = Total basic salary actually earned in <?php echo $selectedYear; ?> &divide; 12</b>
        (Presidential Decree 851 / DOLE). The basic salary earned is taken from recorded
        <b>attendance</b> for the year, so unpaid absences lower it and new hires / resigned
        staff are automatically pro-rated. Overtime, holiday premium and allowances are excluded.
        13th month pay is tax-exempt up to &#8369;90,000.
    </p>
</div>

<!-- YEAR SELECTOR -->
<div class="box">
<div class="box-body">

    <form method="GET" class="form-inline">
        <label for="year" style="margin-right:8px;">
            <i class="fa fa-calendar"></i> Computation Year
        </label>

        <select name="year"
                id="year"
                class="form-control input-sm"
                onchange="this.form.submit()"
                style="width:140px;">

            <?php

            // Years that have attendance data, plus the current year.
            $years = array(intval($currentYear));

            $yq = $conn->query("SELECT DISTINCT YEAR(`date`) AS yr FROM attendance ORDER BY yr DESC");
            if($yq){
                while($yr = $yq->fetch_assoc()){
                    if($yr['yr']){
                        $years[] = intval($yr['yr']);
                    }
                }
            }

            $years = array_unique($years);
            rsort($years);

            foreach($years as $yr){
                $sel = ($yr == $selectedYear) ? 'selected' : '';
                echo "<option value='$yr' $sel>$yr</option>";
            }

            ?>

        </select>
    </form>

</div>
</div>

<!-- EMPLOYEE 13TH MONTH TABLE -->
<div class="box">

<div class="box-header with-border">
    <h3 class="box-title">
        Employee 13th Month Computation &mdash; <?php echo $selectedYear; ?>
    </h3>
</div>

<div class="box-body">

<table id="example1" class="table table-bordered table-striped">

<thead>
<tr>
    <th>Employee ID</th>
    <th>Employee Name</th>
    <th>Date Hired</th>
    <th class="text-center">Months Worked</th>
    <th class="text-right">Basic Salary Earned</th>
    <th class="text-right">13th Month Pay</th>
    <th class="text-right">Released</th>
    <th class="text-right">Balance</th>
    <th class="text-center">Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php

$empq = $conn->query("
    SELECT id
    FROM employees
    ORDER BY lastname ASC, firstname ASC
");

$total_13th     = 0;
$total_released = 0;
$total_balance  = 0;

while($e = $empq->fetch_assoc()){

    $tm = compute_thirteenth_month($conn, $e['id'], $selectedYear);

    if(!$tm['found']){
        continue;
    }

    // Only show employees who actually have 13th month pay
    // (i.e. with recorded attendance / basic salary earned this year).
    if($tm['thirteenth_month'] <= 0.005){
        continue;
    }

    $emp = $tm['employee'];

    $total_13th     += $tm['thirteenth_month'];
    $total_released += $tm['released'];
    $total_balance  += $tm['balance'];

    // Status badge
    if(!$tm['entitled']){
        // Hired after the selected year - no entitlement
        $status = "<span class='label label-default'>Not entitled</span>";
    }
    elseif($tm['thirteenth_month'] <= 0.005){
        // Entitled, but no attendance recorded yet this year
        $status = "<span class='label label-default'>No attendance</span>";
    }
    elseif($tm['balance'] <= 0.005){
        $status = "<span class='label label-success'>Fully released</span>";
    }
    elseif($tm['released'] > 0){
        $status = "<span class='label label-warning'>Partially released</span>";
    }
    else{
        $status = "<span class='label label-primary'>Pending</span>";
    }

    echo "
    <tr>

        <td>".$emp['employee_id']."</td>

        <td>".$emp['lastname'].", ".$emp['firstname']."</td>

        <td>".date('M d, Y', strtotime($tm['date_hired']))."</td>

        <td class='text-center'>".$tm['months_worked']."</td>

        <td class='text-right'>&#8369;".number_format($tm['basic_earned'], 2)."</td>

        <td class='text-right'><b>&#8369;".number_format($tm['thirteenth_month'], 2)."</b></td>

        <td class='text-right'>&#8369;".number_format($tm['released'], 2)."</td>

        <td class='text-right'><b>&#8369;".number_format($tm['balance'], 2)."</b></td>

        <td class='text-center'>".$status."</td>

        <td>
            <a href='thirteenth_month_release.php?id=".$emp['id']."&year=".$selectedYear."&type=Midyear'
               class='btn btn-primary btn-sm'
               onclick=\"return confirm('Release Midyear 13th Month Pay for ".$selectedYear."?')\">
                Midyear
            </a>

            <a href='thirteenth_month_release.php?id=".$emp['id']."&year=".$selectedYear."&type=Year End'
               class='btn btn-success btn-sm'
               onclick=\"return confirm('Release Year End 13th Month Pay for ".$selectedYear."?')\">
                Year End
            </a>

            <a href='thirteenth_month_history.php?id=".$emp['id']."&year=".$selectedYear."'
               class='btn btn-info btn-sm'>
                History
            </a>

            <a href='thirteenth_month_pdf.php?id=".$emp['id']."&year=".$selectedYear."'
               class='btn btn-danger btn-sm'
               target='_blank'>
                PDF
            </a>
        </td>

    </tr>
    ";
}

?>

</tbody>

<tfoot>
<tr style="font-weight:700; background:#f4f7fb;">
    <td colspan="5" class="text-right">TOTAL</td>
    <td class="text-right">&#8369;<?php echo number_format($total_13th, 2); ?></td>
    <td class="text-right">&#8369;<?php echo number_format($total_released, 2); ?></td>
    <td class="text-right">&#8369;<?php echo number_format($total_balance, 2); ?></td>
    <td colspan="2"></td>
</tr>
</tfoot>

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

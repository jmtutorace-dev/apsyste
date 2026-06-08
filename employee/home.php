<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    $eid = intval($emp['id']);

    // attendance days this month
    $month_from = date('Y-m-01');
    $month_to   = date('Y-m-t');
    $cnt = $conn->query("SELECT COUNT(*) AS c FROM attendance WHERE employee_id='$eid' AND date BETWEEN '$month_from' AND '$month_to' AND num_hr > 0")->fetch_assoc();
    $days_this_month = $cnt ? $cnt['c'] : 0;

    $monthly = !empty($emp['rate']) ? $emp['rate'] : 0;
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>Welcome, <?php echo htmlspecialchars($emp['firstname']); ?>!</h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            </ol>
        </section>

        <section class="content">

            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>&#8369;<?php echo number_format($monthly, 0); ?></h3>
                            <p>Monthly Salary</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?php echo $days_this_month; ?></h3>
                            <p>Days Present (this month)</p>
                        </div>
                        <div class="icon"><i class="fa fa-calendar-check-o"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 style="font-size:20px; padding-top:10px;"><?php echo htmlspecialchars($emp['position_name']); ?></h3>
                            <p>Position</p>
                        </div>
                        <div class="icon"><i class="fa fa-id-badge"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3 style="font-size:20px; padding-top:10px;">
                                <?php echo $emp['sched_in'] ? date('g:i A', strtotime($emp['sched_in'])).' - '.date('g:i A', strtotime($emp['sched_out'])) : 'N/A'; ?>
                            </h3>
                            <p>Work Schedule</p>
                        </div>
                        <div class="icon"><i class="fa fa-clock-o"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">My Information</h3></div>
                        <div class="box-body">
                            <table class="table">
                                <tr><td width="40%"><b>Employee ID</b></td><td><?php echo htmlspecialchars($emp['employee_id']); ?></td></tr>
                                <tr><td><b>Full Name</b></td><td><?php echo htmlspecialchars($emp['firstname'].' '.$emp['lastname']); ?></td></tr>
                                <tr><td><b>Department</b></td><td><?php echo htmlspecialchars($emp['department_name'] ?? $emp['department']); ?></td></tr>

                                <tr><td><b>Contact</b></td><td><?php echo htmlspecialchars($emp['contact_info']); ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">Quick Links</h3></div>
                        <div class="box-body">
                            <a href="payslip.php" class="btn btn-primary btn-block"><i class="fa fa-file-text-o"></i> View / Print My Payslip</a>
                            <br>
                            <a href="attendance.php" class="btn btn-success btn-block"><i class="fa fa-calendar-check-o"></i> View My Attendance</a>
                            <br>
                            <a href="thirteenth_month.php" class="btn btn-warning btn-block"><i class="fa fa-gift"></i> My 13th Month Pay</a>
                            <br>
                            <a href="account.php" class="btn btn-default btn-block"><i class="fa fa-user"></i> My Account / Change Password</a>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>

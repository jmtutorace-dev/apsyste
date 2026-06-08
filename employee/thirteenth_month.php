<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    include __DIR__ . '/../admin/includes/thirteenth_month_compute.php';
    $year = isset($_GET['year']) ? intval($_GET['year']) : (int)date('Y');
    $tm = compute_thirteenth_month($conn, $emp['id'], $year);
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>My 13th Month Pay</h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">My 13th Month</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">13th Month Pay &mdash; <?php echo $year; ?></h3>
                            <div class="pull-right">
                                <form method="GET" class="form-inline">
                                    <select name="year" class="form-control input-sm" onchange="this.form.submit()">
                                        <?php for($y = (int)date('Y'); $y >= (int)date('Y') - 4; $y--){ ?>
                                            <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">

                            <?php if(!$tm['entitled']){ ?>
                                <div class="callout callout-warning">
                                    You are not yet entitled to 13th month pay for <?php echo $year; ?>
                                    (entitlement requires at least one month of service within the year).
                                </div>
                            <?php } ?>

                            <table class="table">
                                <tr><td width="55%"><b>Basic Salary Earned (<?php echo $year; ?>)</b></td>
                                    <td align="right">&#8369; <?php echo number_format($tm['basic_earned'], 2); ?></td></tr>
                                <tr><td><b>Months of Service</b></td>
                                    <td align="right"><?php echo $tm['months_worked']; ?></td></tr>
                                <tr style="background:#eafaf1;"><td><b>13th Month Pay (Basic &divide; 12)</b></td>
                                    <td align="right"><b>&#8369; <?php echo number_format($tm['thirteenth_month'], 2); ?></b></td></tr>
                                <tr><td><b>Already Released</b></td>
                                    <td align="right">&#8369; <?php echo number_format($tm['released'], 2); ?></td></tr>
                                <tr style="background:#fef9e7;"><td><b>Remaining Balance</b></td>
                                    <td align="right"><b>&#8369; <?php echo number_format($tm['balance'], 2); ?></b></td></tr>
                            </table>

                            <p class="text-muted" style="font-size:12px;">
                                Computed per DOLE rules: 13th month = total basic salary actually earned
                                during the year &divide; 12. Overtime, holiday premiums and allowances are excluded.
                            </p>

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
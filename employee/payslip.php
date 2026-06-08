<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    // Sensible default: the current semi-monthly cut-off
    $day = (int)date('j');
    if($day >= 16){
        $def_from = date('m/16/Y');
        $def_to   = date('m/t/Y');
    }else{
        $def_from = date('m/01/Y');
        $def_to   = date('m/15/Y');
    }
    $default_range = $def_from.' - '.$def_to;
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>My Payslip</h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">My Payslip</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">Generate Payslip</h3></div>
                        <div class="box-body">

                            <p class="text-muted">
                                Choose your pay period, then click <b>Generate</b>. Pay is computed
                                semi-monthly, so pick a half-month cut-off (the <b>1st&ndash;15th</b>
                                or the <b>16th&ndash;end of the month</b>).
                            </p>

                            <form action="payslip_generate.php" method="POST" target="_blank">
                                <div class="form-group">
                                    <label>Pay Period</label>
                                    <input type="text" class="form-control" id="date_range" name="date_range" value="<?php echo $default_range; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-file-pdf-o"></i> Generate Payslip (PDF)
                                </button>
                            </form>

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

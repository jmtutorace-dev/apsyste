<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>My Account</h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">My Account</li>
            </ol>
        </section>

        <section class="content">

            <?php
                if(isset($_SESSION['success'])){
                    echo "<div class='alert alert-success alert-dismissible'>
                            <button type='button' class='close' data-dismiss='alert'>&times;</button>
                            ".htmlspecialchars($_SESSION['success'])."
                          </div>";
                    unset($_SESSION['success']);
                }
                if(isset($_SESSION['error'])){
                    echo "<div class='alert alert-danger alert-dismissible'>
                            <button type='button' class='close' data-dismiss='alert'>&times;</button>
                            ".htmlspecialchars($_SESSION['error'])."
                          </div>";
                    unset($_SESSION['error']);
                }
            ?>

            <div class="row">

                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">Profile</h3></div>
                        <div class="box-body">
                            <table class="table">
                                <tr><td width="40%"><b>Employee ID</b></td><td><?php echo htmlspecialchars($emp['employee_id']); ?></td></tr>
                                <tr><td><b>Name</b></td><td><?php echo htmlspecialchars($emp['firstname'].' '.$emp['lastname']); ?></td></tr>
                                <tr><td><b>Position</b></td><td><?php echo htmlspecialchars($emp['position_name']); ?></td></tr>
                                <tr><td><b>Department</b></td><td><?php echo htmlspecialchars($emp['department']); ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">Change Password</h3></div>
                        <div class="box-body">
                            <form action="account_update.php" method="POST">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                                    <small class="text-muted">At least 6 characters.</small>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" name="change_password">
                                    <i class="fa fa-key"></i> Update Password
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
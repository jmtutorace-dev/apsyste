<aside class="main-sidebar">
    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo (!empty($emp['photo'])) ? '../images/'.htmlspecialchars($emp['photo']) : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo htmlspecialchars($emp['firstname'].' '.$emp['lastname']); ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Employee</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MENU</li>

            <li>
                <a href="home.php">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="payslip.php">
                    <i class="fa fa-file-text-o"></i> <span>My Payslip</span>
                </a>
            </li>

            <li>
                <a href="attendance.php">
                    <i class="fa fa-calendar-check-o"></i> <span>My Attendance</span>
                </a>
            </li>

            <li>
                <a href="thirteenth_month.php">
                    <i class="fa fa-gift"></i> <span>My 13th Month</span>
                </a>
            </li>

            <li>
                <a href="account.php">
                    <i class="fa fa-user"></i> <span>My Account</span>
                </a>
            </li>

            <li>
                <a href="logout.php">
                    <i class="fa fa-sign-out"></i> <span>Sign Out</span>
                </a>
            </li>
        </ul>

    </section>
</aside>

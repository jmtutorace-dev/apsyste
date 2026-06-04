<aside class="main-sidebar">

    <!-- Sidebar -->
    <section class="sidebar">

        <!-- USER PANEL -->
        <div class="user-panel">

            <div class="pull-left image">
                <img src="<?php echo (!empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg'; ?>"
                     class="img-circle"
                     alt="User Image">
            </div>

            <div class="pull-left info">

                <p style="margin-bottom:3px;">
                    <?php echo $user['firstname'].' '.$user['lastname']; ?>
                </p>

                <a href="#">
                    <i class="fa fa-circle text-success"></i>
                    Online
                </a>

            </div>

        </div>

        <!-- SIDEBAR MENU -->
        <ul class="sidebar-menu" data-widget="tree">

            <!-- DASHBOARD -->
            <li class="header">
                MAIN NAVIGATION
            </li>

            <li class="<?php echo ($page == 'home.php') ? 'active' : ''; ?>">

                <a href="home.php">

                    <i class="fa fa-dashboard"></i>

                    <span>Dashboard</span>

                </a>

            </li>

            <!-- ATTENDANCE -->
            <li class="<?php echo ($page == 'attendance.php') ? 'active' : ''; ?>">

                <a href="attendance.php">

                    <i class="fa fa-calendar-check-o"></i>

                    <span>Attendance</span>

                </a>

            </li>

            <!-- EMPLOYEES -->
            <li class="treeview <?php echo (
                $page == 'employee.php' ||
                $page == 'overtime.php' ||
                $page == 'cashadvance.php' ||
                $page == 'schedule.php'
            ) ? 'active menu-open' : ''; ?>">

                <a href="#">

                    <i class="fa fa-users"></i>

                    <span>Employees</span>

                    <span class="pull-right-container">

                        <i class="fa fa-angle-left pull-right"></i>

                    </span>

                </a>

                <ul class="treeview-menu">

                    <li class="<?php echo ($page == 'employee.php') ? 'active' : ''; ?>">

                        <a href="employee.php">

                            <i class="fa fa-circle-o"></i>

                            Employee List

                        </a>

                    </li>

                    <li class="<?php echo ($page == 'overtime.php') ? 'active' : ''; ?>">

                        <a href="overtime.php">

                            <i class="fa fa-circle-o"></i>

                            Overtime

                        </a>

                    </li>

                    <li class="<?php echo ($page == 'schedule.php') ? 'active' : ''; ?>">

                        <a href="schedule.php">

                            <i class="fa fa-circle-o"></i>

                            Schedules

                        </a>

                    </li>

                </ul>

            </li>

            

            <!-- DEDUCTIONS -->
            <li class="treeview <?php echo (
                $page == 'deduction.php' ||
                $page == 'caf_deduction.php'
            ) ? 'active menu-open' : ''; ?>">

                <a href="#">

                    <i class="fa fa-money"></i>

                    <span>Deductions</span>

                    <span class="pull-right-container">

                        <i class="fa fa-angle-left pull-right"></i>

                    </span>

                </a>

                <ul class="treeview-menu">

                    <li class="<?php echo ($page == 'deduction.php') ? 'active' : ''; ?>">

                        <a href="deduction.php">

                            <i class="fa fa-circle-o"></i>

                            Government Deductions

                        </a>

                    </li>

                    <li class="<?php echo ($page == 'employee_deductions.php') ? 'active' : ''; ?>">
    <a href="employee_deductions.php">
        <i class="fa fa-circle-o"></i>
        Employee Deductions
    </a>
</li>


                    <li class="<?php echo ($page == 'cashadvance.php') ? 'active' : ''; ?>">

                        <a href="cashadvance.php">

                            <i class="fa fa-circle-o"></i>

                            Cash Advance

                        </a>

                    </li>

                    <li class="<?php echo ($page == 'deduction_types.php') ? 'active' : ''; ?>">
    <a href="deduction_types.php">
        <i class="fa fa-circle-o"></i>
        Deduction Types
    </a>
</li>

                </ul>

            </li>
            <!-- DEPARTMENTS -->
            <li class="<?php echo ($page == 'department.php') ? 'active' : ''; ?>">

                <a href="department.php">

                    <i class="fa fa-building"></i>

                    <span>Departments</span>

                </a>

            </li>

            <!-- POSITION -->
            <li class="<?php echo ($page == 'position.php') ? 'active' : ''; ?>">

                <a href="position.php">

                    <i class="fa fa-suitcase"></i>

                    <span>Positions</span>

                </a>

            </li>

            <!-- HOLIDAYS -->
            <li class="<?php echo ($page == 'holidays.php') ? 'active' : ''; ?>">

                <a href="holidays.php">

                    <i class="fa fa-calendar"></i>

                    <span>Holidays</span>

                </a>

            </li>

            <!-- REPORTS -->
            <li class="header">
                REPORTS & PRINTABLES
            </li>

            <!-- PAYROLL -->
            <li class="<?php echo ($page == 'payroll.php') ? 'active' : ''; ?>">

                <a href="payroll.php">

                    <i class="fa fa-files-o"></i>

                    <span>Payroll</span>

                </a>

            </li>

            <li>
    <a href="thirteenth_month.php">
        <i class="fa fa-money"></i>
        <span>13th Month Pay</span>
    </a>
</li>
            <!-- SCHEDULE -->
            <li class="<?php echo ($page == 'schedule_employee.php') ? 'active' : ''; ?>">

                <a href="schedule_employee.php">

                    <i class="fa fa-clock-o"></i>

                    <span>Employee Schedule</span>

                </a>

            </li>

        </ul>

    </section>

</aside>
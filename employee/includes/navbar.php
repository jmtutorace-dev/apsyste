<header class="main-header">

    <a href="home.php" class="logo">
        <span class="logo-mini"><b>A</b>CE</span>
        <span class="logo-lg"><b>ACE</b> Employee</span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?php echo (!empty($emp['photo'])) ? '../images/'.htmlspecialchars($emp['photo']) : '../images/profile.jpg'; ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo htmlspecialchars($emp['firstname'].' '.$emp['lastname']); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="<?php echo (!empty($emp['photo'])) ? '../images/'.htmlspecialchars($emp['photo']) : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
                            <p>
                                <?php echo htmlspecialchars($emp['firstname'].' '.$emp['lastname']); ?>
                                <small><?php echo htmlspecialchars($emp['position_name']); ?></small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="account.php" class="btn btn-default btn-flat">My Account</a>
                            </div>
                            <div class="pull-right">
                                <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    $eid = intval($emp['id']);
    $rows = $conn->query("SELECT * FROM attendance WHERE employee_id='$eid' ORDER BY date DESC");
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>My Attendance</h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">My Attendance</li>
            </ol>
        </section>

        <section class="content">
            <div class="box">
                <div class="box-header"><h3 class="box-title">Attendance Records</h3></div>
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($r = $rows->fetch_assoc()){ ?>
                            <tr>
                                <td><?php echo date('M d, Y (D)', strtotime($r['date'])); ?></td>
                                <td><?php echo ($r['time_in']  && $r['time_in']  != '00:00:00') ? date('g:i A', strtotime($r['time_in']))  : '-'; ?></td>
                                <td><?php echo ($r['time_out'] && $r['time_out'] != '00:00:00') ? date('g:i A', strtotime($r['time_out'])) : '-'; ?></td>
                                <td><?php echo number_format($r['num_hr'], 2); ?></td>
                                <td>
                                    <?php if($r['status'] == 1){ ?>
                                        <span class="label label-success">On Time</span>
                                    <?php } else { ?>
                                        <span class="label label-warning">Late</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>

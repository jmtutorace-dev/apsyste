<?php include 'includes/session.php'; ?>
<?php
  include '../timezone.php';
  include 'includes/payslip_compute.php';   // shared compute_payslip() — SAME math as the payslip/payroll PDFs

  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('-15 day', strtotime($range_to)));
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>Payroll (Semi-Monthly)</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payroll</li>
      </ol>
    </section>

    <section class="content">

      <?php
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> Error!</h4>
                  ".htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8')."
                </div>";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> Success!</h4>
                  ".htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8')."
                </div>";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">

            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="fa fa-info-circle text-muted"></i>
                These figures match the printed payslip &amp; payroll exactly.
              </h3>
              <div class="pull-right">
                <form method="POST" class="form-inline" id="payForm">
                  <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control pull-right col-sm-8"
                           id="reservation" name="date_range"
                           value="<?php echo htmlspecialchars(isset($_GET['range']) ? $_GET['range'] : $range_from.' - '.$range_to, ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                  <button type="button" class="btn btn-success btn-sm btn-flat" id="payroll">
                    <span class="glyphicon glyphicon-print"></span> Payroll
                  </button>
                  <button type="button" class="btn btn-primary btn-sm btn-flat" id="payslip">
                    <span class="glyphicon glyphicon-print"></span> Payslip
                  </button>
                  <button type="button" class="btn btn-default btn-sm btn-flat" id="register">
                    <span class="glyphicon glyphicon-download-alt"></span> Export CSV
                  </button>
                </form>
              </div>
            </div>

            <div class="box-body">
              <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <th>Employee Name</th>
                  <th>Employee ID</th>
                  <th>Monthly Salary</th>
                  <th>Basic Salary</th>
                  <th>Absences</th>
                  <th>Late (min)</th>
                  <th>Total Earnings</th>
                  <th>Total Deductions</th>
                  <th>Net Pay</th>
                </thead>
                <tbody>

                <?php

                  // ---- Pay period ----
                  $to   = date('Y-m-d');
                  $from = date('Y-m-d', strtotime('-15 day', strtotime($to)));

                  if(isset($_GET['range'])){
                    $ex = explode(' - ', $_GET['range']);
                    if(count($ex) == 2){
                      $from = date('Y-m-d', strtotime($ex[0]));
                      $to   = date('Y-m-d', strtotime($ex[1]));
                    }
                  }

                  // ---- Employees with attendance in the period (same set as the PDFs) ----
                  $empstmt = $conn->prepare(
                    "SELECT DISTINCT attendance.employee_id AS empid
                     FROM attendance
                     LEFT JOIN employees ON employees.id = attendance.employee_id
                     WHERE attendance.date BETWEEN ? AND ?
                     ORDER BY employees.lastname ASC, employees.firstname ASC"
                  );
                  $empstmt->bind_param('ss', $from, $to);
                  $empstmt->execute();
                  $empres = $empstmt->get_result();

                  while($erow = $empres->fetch_assoc()){

                    // ONE shared computation — identical to the payslip & payroll PDF
                    $p = compute_payslip($conn, $erow['empid'], $from, $to);
                    if(!$p['found']){ continue; }

                    $emp = $p['employee'];

                    echo "
                      <tr>
                        <td>".htmlspecialchars($emp['lastname'].', '.$emp['firstname'], ENT_QUOTES, 'UTF-8')."</td>
                        <td>".htmlspecialchars($emp['employee_id'], ENT_QUOTES, 'UTF-8')."</td>
                        <td>".number_format($p['monthly'], 2)."</td>
                        <td>".number_format($p['earn']['BASIC SALARY'], 2)."</td>
                        <td>".($p['ded_count']['ABSENCES'] > 0 ? number_format($p['ded_count']['ABSENCES'], 2) : '-')."</td>
                        <td>".($p['ded_count']['LATE'] > 0 ? number_format($p['ded_count']['LATE'], 2) : '-')."</td>
                        <td>".number_format($p['total_earnings'], 2)."</td>
                        <td><b>".number_format($p['total_deductions'], 2)."</b></td>
                        <td><b>".number_format($p['net'], 2)."</b></td>
                      </tr>
                    ";
                  }

                ?>

                </tbody>
              </table>
              </div>

            </div>
          </div>
        </div>
      </div>

    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){

  $("#reservation").on('change', function(){
    var range = encodeURI($(this).val());
    window.location = 'payroll.php?range=' + range;
  });

  $('#payroll').click(function(e){
    e.preventDefault();
    $('#payForm').attr('action', 'payroll_generate.php');
    $('#payForm').submit();
  });

  $('#payslip').click(function(e){
    e.preventDefault();
    $('#payForm').attr('action', 'payslip_generate.php');
    $('#payForm').submit();
  });

  $('#register').click(function(e){
    e.preventDefault();
    $('#payForm').attr('action', 'payroll_register.php');
    $('#payForm').submit();
  });

});
</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
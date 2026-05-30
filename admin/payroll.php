<?php include 'includes/session.php'; ?>
<?php
  include '../timezone.php';
  include 'includes/tax_table.php';

  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('-15 day', strtotime($range_to)));
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">

      <h1>Payroll (Semi-Monthly)</h1>

      <ol class="breadcrumb">

        <li>
          <a href="#">
            <i class="fa fa-dashboard"></i> Home
          </a>
        </li>

        <li class="active">Payroll</li>

      </ol>

    </section>

    <!-- Main Content -->
    <section class="content">

      <?php

        if(isset($_SESSION['error'])){

          echo "
            <div class='alert alert-danger alert-dismissible'>

              <button type='button'
                      class='close'
                      data-dismiss='alert'
                      aria-hidden='true'>

                &times;

              </button>

              <h4>
                <i class='icon fa fa-warning'></i> Error!
              </h4>

              ".$_SESSION['error']."

            </div>
          ";

          unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])){

          echo "
            <div class='alert alert-success alert-dismissible'>

              <button type='button'
                      class='close'
                      data-dismiss='alert'
                      aria-hidden='true'>

                &times;

              </button>

              <h4>
                <i class='icon fa fa-check'></i> Success!
              </h4>

              ".$_SESSION['success']."

            </div>
          ";

          unset($_SESSION['success']);
        }

      ?>

      <div class="row">

        <div class="col-xs-12">

          <div class="box">

            <div class="box-header with-border">

              <div class="pull-right">

                <form method="POST"
                      class="form-inline"
                      id="payForm">

                  <div class="input-group">

                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>

                    <input type="text"
                           class="form-control pull-right col-sm-8"
                           id="reservation"
                           name="date_range"
                           value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from.' - '.$range_to; ?>">

                  </div>

                  <button type="button"
                          class="btn btn-success btn-sm btn-flat"
                          id="payroll">

                    <span class="glyphicon glyphicon-print"></span>
                    Payroll

                  </button>

                  <button type="button"
                          class="btn btn-primary btn-sm btn-flat"
                          id="payslip">

                    <span class="glyphicon glyphicon-print"></span>
                    Payslip

                  </button>

                </form>

              </div>

            </div>

            <div class="box-body">

              <div class="table-responsive">

              <table id="example1"
                     class="table table-bordered table-striped">

                <thead>

                  <th>Employee Name</th>
                  <th>Employee ID</th>
                  <th>Monthly Salary</th>
                  <th>Days Present</th>
                  <th>Worked Hours</th>
                  <th>Base Pay</th>
                  <th>Overtime</th>
                  <th>Holiday Pay</th>
                  <th>Deductions</th>
                  <th>Net Pay</th>

                </thead>

                <tbody>

                <?php

                  $to = date('Y-m-d');

                  $from = date(
                    'Y-m-d',
                    strtotime('-15 day', strtotime($to))
                  );

                  if(isset($_GET['range'])){

                    $range = $_GET['range'];

                    $ex = explode(' - ', $range);

                    $from = date('Y-m-d', strtotime($ex[0]));
                    $to = date('Y-m-d', strtotime($ex[1]));
                  }

                  $sql = "SELECT employees.*,
                                 attendance.employee_id AS empid,
                                 position.rate

                          FROM attendance

                          LEFT JOIN employees
                          ON employees.id = attendance.employee_id

                          LEFT JOIN position
                          ON position.id = employees.position_id

                          WHERE attendance.date BETWEEN '$from' AND '$to'

                          GROUP BY attendance.employee_id

                          ORDER BY employees.lastname ASC,
                                   employees.firstname ASC";

                  $query = $conn->query($sql);

                  while($row = $query->fetch_assoc()){

                    $empid = $row['empid'];

                    $monthly_salary = $row['rate']
                                       ? $row['rate']
                                       : 0;

                    $daily_rate = $monthly_salary / 26;

                    $hourly_rate = $daily_rate / 8;

                    $days_present = 0;
                    $total_worked_hours = 0;
                    $late_deduction = 0;
                    $holiday_pay = 0;
                    $base_pay = 0;

                    $processed_holidays = array();

                    $attsql = "SELECT *
                               FROM attendance
                               WHERE employee_id='$empid'
                               AND date BETWEEN '$from' AND '$to'";

                    $attquery = $conn->query($attsql);

                    while($attrow = $attquery->fetch_assoc()){

                      $worked_hours = $attrow['num_hr'];
                      $attendance_date = $attrow['date'];

                      if($worked_hours > 0){

                        $days_present++;

                        $total_worked_hours += $worked_hours;
                      }

                      // =====================================
                      // LATE / UNDERTIME
                      // =====================================

                      if($worked_hours > 0 && $worked_hours < 8){

                        $undertime_hours = 8 - $worked_hours;

                        $late_deduction += (
                          $undertime_hours * $hourly_rate
                        );
                      }

                      // =====================================
                      // HOLIDAY CHECK
                      // =====================================

                      $isHoliday = false;

                      $hsql = "SELECT *
                               FROM holidays
                               WHERE holiday_date='$attendance_date'";

                      $hquery = $conn->query($hsql);

                      if($hquery->num_rows > 0){

                        $isHoliday = true;

                        $holiday = $hquery->fetch_assoc();

                        $holiday_type = strtolower($holiday['type']);

                        $processed_holidays[] = $attendance_date;

                        // =================================
                        // REGULAR HOLIDAY
                        // =================================

                        if($holiday_type == 'regular'){

                            // PRESENT = DOUBLE PAY

                            if($worked_hours > 0){

                                $holiday_pay += (
                                    ($worked_hours * $hourly_rate) * 2
                                );
                            }

                            // ABSENT = 1 DAY PAY

                            else{

                                $holiday_pay += $daily_rate;
                            }
                        }

                        // =================================
                        // SPECIAL HOLIDAY
                        // =================================

                        else if($holiday_type == 'special'){

                            if($worked_hours > 0){

                                $holiday_pay += (
                                  $worked_hours *
                                  $hourly_rate *
                                  1.30
                                );
                            }
                        }
                      }

                      // =====================================
                      // NORMAL DAY BASE PAY ONLY
                      // =====================================

                      if(!$isHoliday){

                        $base_pay += (
                          $worked_hours * $hourly_rate
                        );
                      }
                    }

                    // =========================================
                    // HOLIDAYS WITHOUT ATTENDANCE
                    // =========================================

                    $holidaysql = "SELECT *
                                   FROM holidays
                                   WHERE holiday_date BETWEEN '$from' AND '$to'";

                    $holidayquery = $conn->query($holidaysql);

                    while($holidayrow = $holidayquery->fetch_assoc()){

                      $holiday_date = $holidayrow['holiday_date'];
                      $holiday_type = strtolower($holidayrow['type']);

                      if(in_array($holiday_date, $processed_holidays)){
                        continue;
                      }

                      // REGULAR HOLIDAY ABSENT = PAID

                      if($holiday_type == 'regular'){

                        $holiday_pay += $daily_rate;
                      }
                    }

                    // =========================================
                    // OVERTIME
                    // =========================================

                    $otsql = "SELECT SUM(hours * rate) AS overtime_pay
                              FROM overtime
                              WHERE employee_id='$empid'
                              AND date_overtime BETWEEN '$from' AND '$to'";

                    $otquery = $conn->query($otsql);

                    $otrow = $otquery->fetch_assoc();

                    $overtime = $otrow['overtime_pay']
                                 ? $otrow['overtime_pay']
                                 : 0;

                    // =========================================
                    // CASH ADVANCE
                    // =========================================

                    $casql = "SELECT SUM(amount) AS cashamount
                              FROM cashadvance
                              WHERE employee_id='$empid'
                              AND date_advance BETWEEN '$from' AND '$to'";

                    $caquery = $conn->query($casql);

                    $carow = $caquery->fetch_assoc();

                    $cashadvance = $carow['cashamount']
                                    ? $carow['cashamount']
                                    : 0;

                    // =========================================
                    // GROSS PAY
                    // =========================================

                    $gross = $base_pay + $holiday_pay + $overtime;

                    // =========================================
// GOVERNMENT DEDUCTIONS
// =========================================

$government_deduction = 0;

$dsql = "SELECT deductions.*
         FROM employee_deductions

         LEFT JOIN deductions
         ON deductions.id = employee_deductions.deduction_id

         WHERE employee_deductions.employee_id = '$empid'";

$dquery = $conn->query($dsql);

while($drow = $dquery->fetch_assoc()){

    $amount = $drow['amount'];
    $type = strtolower($drow['type']);

    if($type == 'percent' || $type == 'percentage'){

        $computed =
            $monthly_salary * ($amount / 100);

    }
    else{

        $computed = $amount;
    }

    $computed = $computed / 2;

    $government_deduction += $computed;
}


// =========================================
// EMPLOYEE DEDUCTIONS (CUSTOM)
// =========================================

$employee_deduction = 0;

$edsql = "
    SELECT SUM(amount) AS employee_deduction
    FROM employee_deductions
    WHERE employee_id='$empid'
    AND created_on BETWEEN '$from' AND '$to'
";

$edquery = $conn->query($edsql);

$edrow = $edquery->fetch_assoc();

$employee_deduction = !empty($edrow['employee_deduction'])
    ? $edrow['employee_deduction']
    : 0;

                    // =========================================
                    // TAX
                    // =========================================

                    $semi_monthly_salary = $monthly_salary / 2;

                    $tax = compute_tax($semi_monthly_salary);

                    // =========================================
                    // TOTAL DEDUCTIONS
                    // =========================================

                    $total_deduction = $government_deduction
                  + $employee_deduction
                  + $cashadvance
                  + $late_deduction
                  + $tax;

                    // =========================================
                    // NET PAY
                    // =========================================

                    $net = $gross - $total_deduction;

                    echo "
                      <tr>

                        <td>
                          ".$row['lastname'].",
                          ".$row['firstname']."
                        </td>

                        <td>
                          ".$row['employee_id']."
                        </td>

                        <td>
                          ".number_format($monthly_salary, 2)."
                        </td>

                        <td>
                          ".$days_present."
                        </td>

                        <td>
                          ".number_format($total_worked_hours, 2)."
                        </td>

                        <td>
                          ".number_format($base_pay, 2)."
                        </td>

                        <td>
                          ".number_format($overtime, 2)."
                        </td>

                        <td>
                          ".number_format($holiday_pay, 2)."
                        </td>

                        <td>
                          <b>".number_format($total_deduction, 2)."</b>
                        </td>

                        <td>
                          <b>".number_format($net, 2)."</b>
                        </td>

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

});

</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
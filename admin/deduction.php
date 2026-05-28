<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/tax_table.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">

      <h1>Deductions</h1>

      <ol class="breadcrumb">

        <li>
          <a href="#">
            <i class="fa fa-dashboard"></i> Home
          </a>
        </li>

        <li class="active">Deductions</li>

      </ol>

    </section>

    <!-- Main content -->
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

      <!-- ===================================================== -->
      <!-- COMPANY DEDUCTIONS -->
      <!-- ===================================================== -->

      <div class="row">

        <div class="col-xs-12">

          <div class="box">

            <div class="box-header with-border">

              <h3 class="box-title">
                Company Deductions
              </h3>

              <div class="pull-right">

                <a href="#addnew"
                   data-toggle="modal"
                   class="btn btn-primary btn-sm btn-flat">

                  <i class="fa fa-plus"></i> New

                </a>

              </div>

            </div>

            <div class="box-body">

              <table id="example1"
                     class="table table-bordered">

                <thead>

                  <th>Description</th>
                  <th>Amount / Percent</th>
                  <th>Type</th>
                  <th width="180">Tools</th>

                </thead>

                <tbody>

                  <?php

                    $sql = "SELECT * FROM deductions";

                    $query = $conn->query($sql);

                    while($row = $query->fetch_assoc()){

                      $display_type = $row['type'];

                      if($display_type == 'percent'){
                        $display_type = 'Percentage';
                      }
                      else{
                        $display_type = 'Fixed';
                      }

                      echo "
                        <tr>

                          <td>".$row['description']."</td>

                          <td>".number_format($row['amount'], 2)."</td>

                          <td>".$display_type."</td>

                          <td>

                            <button class='btn btn-success btn-sm edit btn-flat'
                                    data-id='".$row['id']."'>

                              <i class='fa fa-edit'></i> Edit

                            </button>

                            <button class='btn btn-danger btn-sm delete btn-flat'
                                    data-id='".$row['id']."'>

                              <i class='fa fa-trash'></i> Delete

                            </button>

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

      <!-- SPACE -->
      <div style="margin-top:25px;"></div>

      <!-- ===================================================== -->
      <!-- EMPLOYEE GOVERNMENT DEDUCTIONS -->
      <!-- ===================================================== -->

      <div class="row">

        <div class="col-xs-12">

          <div class="box">

            <div class="box-header with-border">

              <h3 class="box-title">
                Employee Government Deductions
              </h3>

            </div>

            <div class="box-body">

              <table class="table table-bordered table-striped">

                <thead>

                  <tr>

                    <th>Employee</th>
                    <th>Monthly Salary</th>

                    <?php

                      $header_sql = "SELECT * FROM deductions ORDER BY description ASC";
                      $header_query = $conn->query($header_sql);

                      while($header = $header_query->fetch_assoc()){

                        echo "<th>".$header['description']."</th>";
                      }

                    ?>

                    <th>Tax</th>
                    <th>Total Deduction</th>

                  </tr>

                </thead>

                <tbody>

                  <?php

                    // =========================================
                    // GET ALL DEDUCTIONS
                    // =========================================

                    $deductions = array();

                    $dsql = "SELECT * FROM deductions ORDER BY description ASC";
                    $dquery = $conn->query($dsql);

                    while($drow = $dquery->fetch_assoc()){

                      $deductions[] = $drow;
                    }

                    // =========================================
                    // GET EMPLOYEES
                    // =========================================

                    $sql = "SELECT 
                                employees.id,
                                employees.firstname,
                                employees.lastname,
                                employees.employee_id,
                                position.rate

                            FROM employees

                            LEFT JOIN position
                              ON position.id = employees.position_id

                            ORDER BY employees.lastname ASC,
                                     employees.firstname ASC";

                    $query = $conn->query($sql);

                    while($row = $query->fetch_assoc()){

                      $employee_id = $row['id'];

                      $salary = $row['rate']
                                ? $row['rate']
                                : 0;

                      $total_deduction = 0;

                      // =====================================
                      // GET EMPLOYEE ASSIGNED DEDUCTIONS
                      // =====================================

                      $assigned = array();

                      $asql = "SELECT deduction_id 
                               FROM employee_deductions
                               WHERE employee_id = '$employee_id'";

                      $aquery = $conn->query($asql);

                      while($arow = $aquery->fetch_assoc()){

                        $assigned[] = $arow['deduction_id'];
                      }

                      echo "
                        <tr>

                          <td>
                            ".$row['lastname'].", ".$row['firstname']."
                          </td>

                          <td>
                            ".number_format($salary, 2)."
                          </td>
                      ";

                      // =====================================
                      // DISPLAY ONLY ASSIGNED DEDUCTIONS
                      // =====================================

                      foreach($deductions as $ded){

                        $computed = 0;

                        // CHECK IF EMPLOYEE HAS THIS DEDUCTION
                        if(in_array($ded['id'], $assigned)){

                          $value = $ded['amount'];

                          $type = strtolower($ded['type']);

                          // PERCENTAGE
                          if($type == 'percent'
                             || $type == 'percentage'){

                            $computed = $salary * ($value / 100);
                          }

                          // FIXED
                          else{

                            $computed = $value;
                          }

                          $total_deduction += $computed;

                          echo "
                            <td>
                              ".number_format($computed, 2)."
                            </td>
                          ";
                        }

                        // NOT ASSIGNED
                        else{

                          echo "
                            <td>
                              -
                            </td>
                          ";
                        }
                      }

                      // =====================================
                      // TAX
                      // =====================================

                      $tax = compute_tax($salary);

                      $total_deduction += $tax;

                      echo "

                          <td>
                            ".number_format($tax, 2)."
                          </td>

                          <td>
                            <b>".number_format($total_deduction, 2)."</b>
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

    </section>

  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/deduction_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<script>

$(function(){

  $('.edit').click(function(e){

    e.preventDefault();

    $('#edit').modal('show');

    var id = $(this).data('id');

    getRow(id);
  });

  $('.delete').click(function(e){

    e.preventDefault();

    $('#delete').modal('show');

    var id = $(this).data('id');

    getRow(id);
  });

});

function getRow(id){

  $.ajax({

    type: 'POST',

    url: 'deduction_row.php',

    data: {id:id},

    dataType: 'json',

    success: function(response){

      $('.decid').val(response.id);

      $('#edit_description').val(response.description);

      $('#edit_amount').val(response.amount);

      $('#edit_type').val(response.type);

      $('#del_deduction').html(response.description);
    }
  });
}

</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
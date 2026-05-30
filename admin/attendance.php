<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>Attendance</h1>
    </section>

    <section class="content">

      <?php
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
          unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">

          <div class="box">

            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                <i class="fa fa-plus"></i> New
              </a>
            </div>

            <div class="box-body">

              <table id="example1" class="table table-bordered table-striped">

                <thead>
                  <tr>
                    <th class="hidden"></th>
                    <th>Date</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Tools</th>
                  </tr>
                </thead>

                <tbody>

                <?php

                  $sql = "
                    SELECT *,
                    employees.employee_id AS empid,
                    attendance.id AS attid
                    FROM attendance
                    LEFT JOIN employees
                    ON employees.id = attendance.employee_id
                    ORDER BY attendance.date DESC,
                             attendance.time_in DESC
                  ";

                  $query = $conn->query($sql);

                  while($row = $query->fetch_assoc()){

                    $sched_in = '';
                    $sched_out = '';

                    if(!empty($row['schedule_id'])){

                      $sched_sql = "
                        SELECT *
                        FROM schedules
                        WHERE id = '".$row['schedule_id']."'
                      ";

                      $sched_query = $conn->query($sched_sql);

                      if($sched_query && $sched_query->num_rows > 0){

                        $sched = $sched_query->fetch_assoc();

                        $sched_in = $sched['time_in'];
                        $sched_out = $sched['time_out'];
                      }
                    }

                    $status = '
                      <span class="label label-default pull-right">
                        No Schedule
                      </span>
                    ';

                    $undertime = '';

                    if($sched_in != ''){

                      if(strtotime($row['time_in']) > strtotime($sched_in)){

                        $status = '
                          <span class="label label-danger pull-right">
                            Late
                          </span>
                        ';
                      }
                      else{

                        $status = '
                          <span class="label label-success pull-right">
                            On Time
                          </span>
                        ';
                      }

                      if(
                        !empty($row['time_out']) &&
                        strtotime($row['time_out']) < strtotime($sched_out)
                      ){

                        $undertime = '
                          <span class="label label-warning pull-right"
                                style="margin-left:5px;">

                            Undertime

                          </span>
                        ';
                      }
                    }

                    echo "

                      <tr>

                        <td class='hidden'></td>

                        <td data-order='".date('Y-m-d', strtotime($row['date']))."'>

                          ".date('M d, Y', strtotime($row['date']))."

                        </td>

                        <td>".$row['empid']."</td>

                        <td>".$row['firstname'].' '.$row['lastname']."</td>

                        <td>

                          ".date('h:i A', strtotime($row['time_in']))."

                          ".$status."

                        </td>

                        <td>

                          ".date('h:i A', strtotime($row['time_out']))."

                          ".$undertime."

                        </td>

                        <td>

                          <button
                            class='btn btn-success btn-sm btn-flat edit'
                            data-id='".$row['attid']."'>

                            <i class='fa fa-edit'></i> Edit

                          </button>

                          <button
                            class='btn btn-danger btn-sm btn-flat delete'
                            data-id='".$row['attid']."'>

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

    </section>

  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/attendance_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<style>

#example1_filter{
  white-space: nowrap;
  width: 100%;
  margin-left: -40px;
}

#example1_filter label{
  display: inline-flex !important;
  align-items: center;
  margin-right: 4px;
  margin-bottom: 0 !important;
  vertical-align: middle;
  font-size: 12px;
}

#example1_filter input[type="search"]{
  width: 120px !important;
  height: 28px !important;
  margin-left: 3px;
}

#dateFilterWrapper{
  display: inline-flex;
  align-items: center;
  margin-left: 4px;
  vertical-align: middle;
  gap: 4px;
  font-size: 12px;
}

#dateFilter{
  width: 120px !important;
  height: 28px !important;
  padding: 2px 5px;
}

#clearDate{
  height: 28px !important;
  padding: 3px 8px !important;
  font-size: 12px !important;
}

</style>

<script>

$(function(){

  // =========================
  // DATATABLE
  // =========================

  var table = $('#example1').DataTable({
    order: [[1, 'desc']]
  });

  // =========================
  // ADD DATE FILTER
  // =========================

  setTimeout(function(){

    var filterHTML = `

      <span id="dateFilterWrapper">

        <span>Date:</span>

        <input type="date"
               id="dateFilter"
               class="form-control input-sm">

        <button type="button"
                id="clearDate"
                class="btn btn-default btn-sm">

          Clear

        </button>

      </span>

    `;

    $('#example1_filter').append(filterHTML);

  }, 300);

  // =========================
  // DATE FILTER LOGIC
  // =========================

  $.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex){

      var selectedDate = $('#dateFilter').val();

      if(selectedDate == ''){
        return true;
      }

      var rowNode = table.row(dataIndex).node();

      var actualDate = $(rowNode)
        .find('td:eq(1)')
        .attr('data-order');

      return actualDate == selectedDate;
    }
  );

  // =========================
  // APPLY FILTER
  // =========================

  $(document).on('change', '#dateFilter', function(){

    table.draw();

  });

  // =========================
  // CLEAR FILTER
  // =========================

  $(document).on('click', '#clearDate', function(){

    $('#dateFilter').val('');

    table.draw();

  });

  // =========================
  // EDIT
  // =========================

  $('.edit').click(function(e){

    e.preventDefault();

    $('#edit').modal('show');

    getRow($(this).data('id'));

  });

  // =========================
  // DELETE
  // =========================

  $('.delete').click(function(e){

    e.preventDefault();

    $('#delete').modal('show');

    getRow($(this).data('id'));

  });

});

// =========================
// GET ROW
// =========================

function getRow(id){

  $.ajax({

    type: 'POST',

    url: 'attendance_row.php',

    data: {id:id},

    dataType: 'json',

    success: function(response){

      $('#datepicker_edit').val(response.date);

      $('#edit_time_in').val(response.time_in);

      $('#edit_time_out').val(response.time_out);

      $('#attid').val(response.attid);

      $('#employee_name').html(
        response.firstname + ' ' + response.lastname
      );

      $('#del_attid').val(response.attid);

      $('#del_employee_name').html(
        response.firstname + ' ' + response.lastname
      );
    }
  });
}

</script>

</body>
</html>
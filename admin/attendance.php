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

              <table id="example1" class="table table-bordered">

                <thead>
                  <th class="hidden"></th>
                  <th>Date</th>
                  <th>Employee ID</th>
                  <th>Name</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                  <th>Tools</th>
                </thead>

                <tbody>

                <?php

                  $sql = "
                    SELECT *,
                    employees.employee_id AS empid,
                    attendance.id AS attid
                    FROM attendance
                    LEFT JOIN employees ON employees.id = attendance.employee_id
                    ORDER BY attendance.date DESC, attendance.time_in DESC
                  ";

                  $query = $conn->query($sql);

                  while($row = $query->fetch_assoc()){

                    /* =========================
                       GET EMPLOYEE SCHEDULE
                    ========================== */

                    $sched_in = '';
                    $sched_out = '';

                    if(!empty($row['schedule_id'])){

                        $sched_sql = "SELECT * FROM schedules WHERE id = '".$row['schedule_id']."'";
                        $sched_query = $conn->query($sched_sql);

                        if($sched_query && $sched_query->num_rows > 0){
                            $sched = $sched_query->fetch_assoc();
                            $sched_in = $sched['time_in'];
                            $sched_out = $sched['time_out'];
                        }
                    }

                    /* =========================
                       DEFAULT LABELS
                    ========================== */

                    $status = '<span class="label label-default pull-right">No Schedule</span>';
                    $undertime = '';

                    /* =========================
                       LATE / ON TIME
                    ========================== */

                    if($sched_in != ''){

                        if(strtotime($row['time_in']) > strtotime($sched_in)){
                            $status = '<span class="label label-danger pull-right">Late</span>';
                        } else {
                            $status = '<span class="label label-success pull-right">On Time</span>';
                        }

                        /* =========================
                           UNDERTIME
                        ========================== */

                        if(!empty($row['time_out']) && strtotime($row['time_out']) < strtotime($sched_out)){
                            $undertime = '<span class="label label-warning pull-right" style="margin-left:5px;">Undertime</span>';
                        }
                    }

                    echo "
                      <tr>

                        <td class='hidden'></td>

                        <td>".date('M d, Y', strtotime($row['date']))."</td>

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
                          <button class='btn btn-success btn-sm btn-flat edit' data-id='".$row['attid']."'>
                            <i class='fa fa-edit'></i> Edit
                          </button>

                          <button class='btn btn-danger btn-sm btn-flat delete' data-id='".$row['attid']."'>
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

<script>
$(function(){

  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    getRow($(this).data('id'));
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    getRow($(this).data('id'));
  });

});

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

      $('#employee_name').html(response.firstname+' '+response.lastname);
      $('#del_attid').val(response.attid);
      $('#del_employee_name').html(response.firstname+' '+response.lastname);
    }
  });
}
</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
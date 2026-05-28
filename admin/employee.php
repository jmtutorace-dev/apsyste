<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
  <h1>Employee List</h1>
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

<div class="box">

<div class="box-header with-border">
  <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm">
    <i class="fa fa-plus"></i> New
  </a>
</div>

<div class="box-body">

<table id="example1" class="table table-bordered">

<thead>
  <th>Employee ID</th>
  <th>Photo</th>
  <th>Name</th>
  <th>Position</th>
  <th>Schedule</th>
  <th>Department</th>
  <th>Member Since</th>
  <th>Tools</th>
</thead>

<tbody>

<?php
$sql = "SELECT 
          employees.*,
          employees.id AS empid,
          position.description AS position_name,
          schedules.time_in,
          schedules.time_out
        FROM employees
        LEFT JOIN position ON position.id = employees.position_id
        LEFT JOIN schedules ON schedules.id = employees.schedule_id";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){
?>

<tr class="employee-row" data-id="<?php echo $row['empid']; ?>" data-dept="<?php echo $row['department']; ?>" style="cursor:pointer;">

  <td><?php echo $row['employee_id']; ?></td>

  <td>
    <img src="<?php echo (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg'; ?>" width="30">
    <a href="#edit_photo" data-toggle="modal" class="photo" data-id="<?php echo $row['empid']; ?>">
      <span class="fa fa-edit"></span>
    </a>
  </td>

  <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
  <td><?php echo $row['position_name']; ?></td>
  <td><?php echo date('h:i A', strtotime($row['time_in'])).' - '.date('h:i A', strtotime($row['time_out'])); ?></td>
  <td><?php echo $row['department']; ?></td>
  <td><?php echo date('M d, Y', strtotime($row['created_on'])); ?></td>

  <td>
    <button class="btn btn-success btn-sm edit" data-id="<?php echo $row['empid']; ?>">Edit</button>
    <button class="btn btn-danger btn-sm delete" data-id="<?php echo $row['empid']; ?>">Delete</button>
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
<?php include 'includes/employee_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<script>

$(function(){

  $('.edit').click(function(e){
    e.stopPropagation();
    $('#edit').modal('show');
    getRow($(this).data('id'));
  });

  $('.delete').click(function(e){
    e.stopPropagation();
    $('#delete').modal('show');
    getRow($(this).data('id'));
  });

  $('.photo').click(function(e){
    e.stopPropagation();
    getRow($(this).data('id'));
  });

});

// CLICK ROW → VIEW MODAL
$(document).on('click', '.employee-row', function(e){

  if($(e.target).closest('button,a').length > 0) return;

  var id = $(this).data('id');

  $.ajax({
    type: 'POST',
    url: 'employee_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){

      $('#view_employee').modal('show');

      $('#view_photo').attr('src',
        response.photo ? '../images/' + response.photo : '../images/profile.jpg'
      );

      $('#view_name').html(response.firstname + ' ' + response.lastname);
      $('#view_employee_id').html(response.employee_id);

      $('#view_address').html(response.address);
      $('#view_birthdate').html(response.birthdate);
      $('#view_contact').html(response.contact_info);
      $('#view_gender').html(response.gender);
      $('#view_position').html(response.position_name);

// 🔥 ADD THIS
$('#view_benefits').html(response.benefits);

      $('#view_position').html(response.position_name);
      $('#view_department').html(response.department);

      $('#view_schedule').html(response.time_in + ' - ' + response.time_out);
    }
  });
});

// EDIT FUNCTION

function getRow(id){

  $.ajax({
    type:'POST',
    url:'employee_row.php',
    data:{id:id},
    dataType:'json',
    success:function(response){

      console.log(response);

      // IMPORTANT
      $('.empid').val(response.empid);

      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_address').val(response.address);
      $('#datepicker_edit').val(response.birthdate);
      $('#edit_contact').val(response.contact_info);

      $('#edit_gender').val(response.gender);
      $('#edit_position').val(response.position_id);
      $('#edit_schedule').val(response.schedule_id);
      $('#edit_department').val(response.department);

      // LOAD DEDUCTIONS
  
$.ajax({
    type: 'POST',
    url: 'includes/employee_fetch_deductions.php',
    data: {id: response.empid},
    success: function(html){
        $('#deduction_container').html(html);
    }
});


    }
  });
}


</script>

<!-- ✅ MOVE FILTER NEXT TO SEARCH BOX -->
<script>
$(document).ready(function(){

  setTimeout(function(){

    var filterHTML = `
      <label style="margin-left:10px;">Department:</label>
      <select id="deptFilter" class="form-control input-sm" style="width:200px; display:inline-block; margin-left:5px;">
        <option value="all">All</option>
      </select>
    `;

    $('#example1_filter').append(filterHTML);

    $.ajax({
      url: 'employee_departments.php',
      method: 'GET',
      success: function(data){
        $('#deptFilter').append(data);
      }
    });

  }, 300);

});

// FILTER LOGIC
$(document).on('change', '#deptFilter', function(){

  var selected = $(this).val();

  $('.employee-row').each(function(){

    var dept = $(this).data('dept');

    if(selected === 'all'){
      $(this).show();
    }
    else{
      (dept === selected) ? $(this).show() : $(this).hide();
    }

  });

});
</script>

<?php include 'includes/datatable_initializer.php'; ?>
</body>
</html>
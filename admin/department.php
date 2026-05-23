<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
  <h1>Departments</h1>
</section>

<section class="content">

<?php
if(isset($_SESSION['error'])){
  echo "<div class='alert alert-danger alert-dismissible'>
  <button type='button' class='close' data-dismiss='alert'>&times;</button>
  <b>Error!</b> ".$_SESSION['error']."</div>";
  unset($_SESSION['error']);
}

if(isset($_SESSION['success'])){
  echo "<div class='alert alert-success alert-dismissible'>
  <button type='button' class='close' data-dismiss='alert'>&times;</button>
  <b>Success!</b> ".$_SESSION['success']."</div>";
  unset($_SESSION['success']);
}
?>

<div class="box">

  <div class="box-header with-border">

    <!-- FIXED BUTTON -->
    <button type="button"
            class="btn btn-primary btn-sm btn-flat"
            data-toggle="modal"
            data-target="#addnew">
      <i class="fa fa-plus"></i> New Department
    </button>

  </div>

  <div class="box-body">

    <table id="example1" class="table table-bordered">
      <thead>
        <tr>
          <th>Department Name</th>
          <th>Tools</th>
        </tr>
      </thead>

      <tbody>
      <?php
      $sql = "SELECT * FROM departments";
      $query = $conn->query($sql);

      if($query){
        while($row = $query->fetch_assoc()){
          echo "
          <tr>
            <td>".$row['name']."</td>
            <td>
              <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'>
                <i class='fa fa-edit'></i>
              </button>

              <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'>
                <i class='fa fa-trash'></i>
              </button>
            </td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='2'>".$conn->error."</td></tr>";
      }
      ?>
      </tbody>

    </table>

  </div>
</div>

</section>
</div>

<?php include 'includes/footer.php'; ?>

<!-- IMPORTANT: MODAL MUST BE HERE -->
<?php include 'includes/department_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){

  // EDIT
  $('.edit').click(function(){
    $('#edit').modal('show');
    getRow($(this).data('id'));
  });

  // DELETE
  $('.delete').click(function(){
    $('#delete').modal('show');
    getRow($(this).data('id'));
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'department_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.deptid').val(response.id);
      $('#edit_name').val(response.name);
      $('#del_name').html(response.name);
    }
  });
}
</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
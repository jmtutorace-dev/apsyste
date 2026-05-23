<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
  <h1>Positions</h1>
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
  <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
    <i class="fa fa-plus"></i> New Position
  </a>
</div>

<div class="box-body">

<table id="example1" class="table table-bordered">

<thead>
  <th>Position Title</th>
  <th>Monthly Salary</th>
  <th>Tools</th>
</thead>

<tbody>

<?php
$sql = "SELECT * FROM position";
$query = $conn->query($sql);

while($row = $query->fetch_assoc()){
?>

<tr>
  <td><?php echo $row['description']; ?></td>

  <!-- CHANGED: NOW MONTHLY SALARY -->
  <td><?php echo number_format($row['rate'], 2); ?></td>

  <td>
    <button class="btn btn-success btn-sm edit btn-flat" data-id="<?php echo $row['id']; ?>">
      <i class="fa fa-edit"></i> Edit
    </button>

    <button class="btn btn-danger btn-sm delete btn-flat" data-id="<?php echo $row['id']; ?>">
      <i class="fa fa-trash"></i> Delete
    </button>
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
<?php include 'includes/position_modal.php'; ?>
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
    url: 'position_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#posid').val(response.id);
      $('#edit_title').val(response.description);

      // NOW THIS IS MONTHLY SALARY
      $('#edit_rate').val(response.rate);

      $('#del_posid').val(response.id);
      $('#del_position').html(response.description);
    }
  });
}
</script>

<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
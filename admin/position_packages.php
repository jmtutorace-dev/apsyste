<?php
include 'includes/session.php';
include 'includes/header.php';

$position = $_GET['position'];
?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">

    <h1>
    <?php echo $position; ?> Salary Rates
</h1>

</section>

<section class="content">

<div class="box">

<div class="box-header clearfix">

    <a href="position.php"
       class="btn btn-default pull-left">

       <i class="fa fa-arrow-left"></i>
       Back

    </a>

    <a href="#addnew"
       data-toggle="modal"
       class="btn btn-primary pull-right">

       <i class="fa fa-plus"></i>
       Add Salary

    </a>

</div>

<div class="box-body">

<table class="table table-bordered">

<thead>

<tr>

    <th>Salary Amount</th>
    <th width="220">Actions</th>

</tr>

</thead>

<tbody>

<?php

$sql = "
SELECT *
FROM position
WHERE description = '$position'
ORDER BY rate ASC
";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

?>

<tr>

    <td>

        ₱<?php echo number_format($row['rate'],2); ?>

    </td>

    <td>

        <button
            class="btn btn-success btn-sm edit"
            data-id="<?php echo $row['id']; ?>">

            <i class="fa fa-edit"></i>
            Edit

        </button>

        <button
            class="btn btn-danger btn-sm delete"
            data-id="<?php echo $row['id']; ?>">

            <i class="fa fa-trash"></i>
            Delete

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
</div>


<?php include 'includes/position_modal.php'; ?>

</div><!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){

    $('.edit').click(function(e){
        e.preventDefault();

        var id = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: 'position_row.php',
            data: {id:id},
            dataType: 'json',
            success: function(response){

                $('#edit').modal('show');

                $('#posid').val(response.id);
                $('#edit_title').val(response.description);
                $('#edit_rate').val(response.rate);

            }
        });
    });

    $('.delete').click(function(e){
        e.preventDefault();

        var id = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: 'position_row.php',
            data: {id:id},
            dataType: 'json',
            success: function(response){

                $('#delete').modal('show');

                $('#del_posid').val(response.id);
                $('#del_position').html(response.description);

            }
        });
    });

});
</script>

</body>
</html>
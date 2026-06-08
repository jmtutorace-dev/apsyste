<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>Deduction Types</h1>
        </section>

        <section class="content">

            <?php

            if(isset($_SESSION['error'])){
                echo "
                <div class='alert alert-danger'>
                    ".$_SESSION['error']."
                </div>
                ";
                unset($_SESSION['error']);
            }

            if(isset($_SESSION['success'])){
                echo "
                <div class='alert alert-success'>
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

                            <a href="#addnew"
                               data-toggle="modal"
                               class="btn btn-primary btn-sm btn-flat">

                                <i class="fa fa-plus"></i>
                                New Deduction Type

                            </a>

                        </div>

                        <div class="box-body">

                            <table id="example1"
                                   class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>ID</th>
                                        <th>Deduction Name</th>
                                        <th>Description</th>
                                        <th>Tools</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php

                                $sql = "
                                    SELECT *
                                    FROM deduction_types
                                    ORDER BY deduction_name ASC
                                ";

                                $query = $conn->query($sql);

                                while($row = $query->fetch_assoc()){

                                    echo "
                                    <tr>

                                        <td>".$row['id']."</td>

                                        <td>".htmlspecialchars($row['deduction_name'], ENT_QUOTES, 'UTF-8')."</td>

                                        <td>".htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8')."</td>

                                        <td>

                                            <button
                                                class='btn btn-success btn-sm edit btn-flat'
                                                data-id='".$row['id']."'>
                                                <i class='fa fa-edit'></i>
                                                Edit
                                            </button>

                                            <button
                                                class='btn btn-danger btn-sm delete btn-flat'
                                                data-id='".$row['id']."'>
                                                <i class='fa fa-trash'></i>
                                                Delete
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

</div>

<!-- ADD MODAL -->

<div class="modal fade" id="addnew">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST"
                  action="deduction_type_add.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Add Deduction Type
                    </h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>
                            Deduction Name
                        </label>

                        <input type="text"
                               name="deduction_name"
                               class="form-control"
                               required>

                    </div>

                    <div class="form-group">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">

                        Close

                    </button>

                    <button type="submit"
                            name="add"
                            class="btn btn-primary">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- EDIT MODAL -->

<div class="modal fade" id="edit">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST"
                  action="deduction_type_edit.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Edit Deduction Type
                    </h4>

                </div>

                <div class="modal-body">

                    <input type="hidden"
                           id="edit_id"
                           name="id">

                    <div class="form-group">

                        <label>
                            Deduction Name
                        </label>

                        <input type="text"
                               id="edit_name"
                               name="deduction_name"
                               class="form-control"
                               required>

                    </div>

                    <div class="form-group">

                        <label>
                            Description
                        </label>

                        <textarea
                            id="edit_description"
                            name="description"
                            class="form-control"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            name="edit"
                            class="btn btn-success">

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- DELETE MODAL -->

<div class="modal fade" id="delete">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST"
                  action="deduction_type_delete.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Delete Deduction Type
                    </h4>

                </div>

                <div class="modal-body">

                    <input type="hidden"
                           id="delete_id"
                           name="id">

                    <p>

                        Are you sure you want to delete this deduction type?

                    </p>

                    <h4 id="delete_name"></h4>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            name="delete"
                            class="btn btn-danger">

                        Delete

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include 'includes/scripts.php'; ?>

<script>

$(function(){

    $('#example1').DataTable();

    $('.edit').click(function(){

        $('#edit').modal('show');

        var id = $(this).data('id');

        getRow(id);

    });

    $('.delete').click(function(){

        $('#delete').modal('show');

        var id = $(this).data('id');

        getRow(id);

    });

});

function getRow(id){

    $.ajax({

        type: 'POST',

        url: 'deduction_type_row.php',

        data: {id:id},

        dataType: 'json',

        success: function(response){

            $('#edit_id').val(response.id);
            $('#edit_name').val(response.deduction_name);
            $('#edit_description').val(response.description);

            $('#delete_id').val(response.id);
            $('#delete_name').html(response.deduction_name);

        }

    });

}

</script>

</body>
</html>
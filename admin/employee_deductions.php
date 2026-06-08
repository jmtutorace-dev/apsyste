<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>Employee Deductions</h1>
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
                                Add Employee Deduction

                            </a>

                        </div>

                        <div class="box-body">

                            <table id="example1"
                                   class="table table-bordered">

                                <thead>

                                <tr>
    <th>Employee</th>
    <th>Deduction Type</th>
    <th>Amount</th>
    <th>Deduction Date</th>
    <th>Tools</th>
</tr>

                                </thead>

                                <tbody>

                                <?php

                                $sql = "
    SELECT
        ed.*,
        e.firstname,
        e.lastname,
        dt.deduction_name
    FROM employee_deductions ed
    INNER JOIN employees e
        ON e.id = ed.employee_id
    INNER JOIN deduction_types dt
        ON dt.id = ed.deduction_id
    WHERE ed.employee_id > 0
    ORDER BY ed.id DESC
";

                                $query = $conn->query($sql);

                                while($row = $query->fetch_assoc()){

    if(empty($row['firstname']) || empty($row['lastname'])){
        continue;
    }

    echo "
<tr>

    <td>".htmlspecialchars($row['firstname'].' '.$row['lastname'], ENT_QUOTES, 'UTF-8')."</td>

    <td>".htmlspecialchars($row['deduction_name'], ENT_QUOTES, 'UTF-8')."</td>

    <td>₱".number_format($row['amount'],2)."</td>

    <td>".$row['created_on']."</td>


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
<?php

$total_deductions = 0;

$total = $conn->query("
    SELECT SUM(amount) AS total
    FROM employee_deductions
");

if($total->num_rows > 0){
    $trow = $total->fetch_assoc();
    $total_deductions = $trow['total'];
}

?>

<div class="alert alert-info">

    <b>Total Employee Deductions:</b>
    ₱<?php echo number_format($total_deductions,2); ?>

</div>
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
                  action="employee_deduction_add.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Add Employee Deduction
                    </h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Employee</label>

                        <select name="employee_id"
                                class="form-control"
                                required>

                            <option value="">
                                Select Employee
                            </option>

                            <?php

                            $emp = $conn->query("
                                SELECT *
                                FROM employees
                                ORDER BY lastname ASC
                            ");

                            while($erow = $emp->fetch_assoc()){

                                echo "
                                <option value='".$erow['id']."'>
                                    ".$erow['lastname'].",
                                    ".$erow['firstname']."
                                </option>
                                ";
                            }

                            ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Deduction Type</label>

                        <select name="deduction_id"
                                class="form-control"
                                required>

                            <option value="">
                                Select Deduction
                            </option>

                            <?php

                            $ded = $conn->query("
                                SELECT *
                                FROM deduction_types
                                ORDER BY deduction_name ASC
                            ");

                            while($drow = $ded->fetch_assoc()){

                                echo "
                                <option value='".$drow['id']."'>
                                    ".$drow['deduction_name']."
                                </option>
                                ";
                            }

                            ?>

                        </select>

                    </div>

                    <div class="form-group">

    <label>Amount</label>

    <input type="number"
           step="0.01"
           name="amount"
           class="form-control"
           required>

</div>

<div class="form-group">

    <label>Deduction Date</label>

    <input type="date"
           name="created_on"
           class="form-control"
           value="<?php echo date('Y-m-d'); ?>"
           required>

</div>

                </div>

                <div class="modal-footer">

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
                  action="employee_deduction_edit.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Edit Deduction
                    </h4>

                </div>

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="edit_id">

                    <div class="form-group">

                        <label>Amount</label>

                        <input type="number"
                               step="0.01"
                               name="amount"
                               id="edit_amount"
                               class="form-control">

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
                  action="employee_deduction_delete.php">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Delete Deduction
                    </h4>

                </div>

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="delete_id">

                    <p>
                        Delete this deduction?
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

        type:'POST',

        url:'employee_deduction_row.php',

        data:{id:id},

        dataType:'json',

        success:function(response){

            $('#edit_id').val(response.id);
            $('#edit_amount').val(response.amount);

            $('#delete_id').val(response.id);

            $('#delete_name').html(
                response.firstname +
                ' ' +
                response.lastname +
                ' - ' +
                response.deduction_name
            );

        }

    });

}

</script>

</body>
</html>
<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<style>

/* =========================================================
   PAGE
========================================================= */

.content-header h1{
    font-weight:700;
    color:#183b56;
}

.box{
    border:none;
    border-radius:18px;
    overflow:hidden;
    background:#ffffff;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.box-header{
    padding:18px 20px;
    border-bottom:1px solid #eef2f7;
    background:#fff;
}

.box-body{
    padding:18px;
    overflow-x:auto;
}

/* =========================================================
   TABLE CLEAN DESIGN
========================================================= */

#example1{
    width:100% !important;
    border-collapse:collapse !important;
    background:#fff;
}

/* HEADER */

#example1 thead th{

    background:#f4f7fb;

    color:#44576d;

    font-size:12px;

    text-transform:uppercase;

    letter-spacing:.5px;

    font-weight:700;

    padding:14px 12px;

    border-bottom:2px solid #e4ebf3 !important;

    white-space:nowrap;
}

/* BODY */

#example1 tbody td{

    padding:14px 12px;

    vertical-align:middle;

    border-top:1px solid #eef2f7 !important;

    background:#fff;

    font-size:13px;

    color:#2c3e50;
}

/* HOVER */

#example1 tbody tr:hover{

    background:#f8fbff !important;
}

#example1 tbody tr:hover td{

    background:#f8fbff !important;
}

/* REMOVE CONFUSING EFFECT */

#example1 tbody tr{
    transition:.15s;
}

/* PHOTO */

.emp-photo{

    width:38px;

    height:38px;

    object-fit:cover;

    border-radius:50%;

    border:2px solid #eef2f7;
}

/* TOOLS */

.tool-buttons{
    white-space:nowrap;
}

.tool-buttons .btn{
    margin-right:5px;
    border-radius:8px !important;
    font-size:12px;
    padding:6px 12px;
}

/* SEARCH + FILTER INLINE */

.dataTables_filter{

    display:flex !important;

    align-items:center;

    justify-content:flex-end;

    gap:10px;

    flex-wrap:wrap;

    margin-bottom:15px;
}

.dataTables_filter label{
    margin:0 !important;
}

.dataTables_filter input{

    height:36px !important;

    border-radius:8px;

    border:1px solid #dbe5ef;

    padding:6px 12px;
}

#deptFilter{

    width:180px !important;

    height:36px !important;

    border-radius:8px;

    border:1px solid #dbe5ef;

    padding:6px 10px;

    background:#fff;
}

/* ALERTS */

.alert{
    border:none;
    border-radius:10px;
}

</style>

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

<div class="box">

<div class="box-header with-border">

    <a href="#addnew"
       data-toggle="modal"
       class="btn btn-primary btn-sm">

       <i class="fa fa-plus"></i> New Employee

    </a>

</div>

<div class="box-body">

<table id="example1" class="table">

<thead>
<tr>

    <th>Employee ID</th>
    <th>Photo</th>
    <th>Name</th>
    <th>Position</th>
    <th>Schedule</th>
    <th>Department</th>
    <th>Member Since</th>
    <th>Tools</th>

</tr>
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

<tr class="employee-row"
    data-id="<?php echo $row['empid']; ?>"
    data-dept="<?php echo $row['department']; ?>"
    style="cursor:pointer;">

    <td>
        <?php echo $row['employee_id']; ?>
    </td>

    <td>

        <img
            src="<?php echo (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg'; ?>"
            class="emp-photo"
        >

        <a href="#edit_photo"
           data-toggle="modal"
           class="photo"
           data-id="<?php echo $row['empid']; ?>">

           <i class="fa fa-edit"></i>

        </a>

    </td>

    <td>
        <?php echo $row['firstname'].' '.$row['lastname']; ?>
    </td>

    <td>
        <?php echo $row['position_name']; ?>
    </td>

    <td>

        <?php
        echo date('h:i A', strtotime($row['time_in']))
        .' - '.
        date('h:i A', strtotime($row['time_out']));
        ?>

    </td>

    <td>
        <?php echo $row['department']; ?>
    </td>

    <td>
        <?php echo date('M d, Y', strtotime($row['created_on'])); ?>
    </td>

    <td class="tool-buttons">

        <button class="btn btn-success btn-sm edit"
                data-id="<?php echo $row['empid']; ?>">

            Edit

        </button>

        <button class="btn btn-danger btn-sm delete"
                data-id="<?php echo $row['empid']; ?>">

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

/* =========================================================
   VIEW EMPLOYEE
========================================================= */

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

            $('#view_photo').attr(
                'src',
                response.photo ?
                '../images/' + response.photo :
                '../images/profile.jpg'
            );

            $('#view_name').html(
                response.firstname + ' ' + response.lastname
            );

            $('#view_employee_id').html(
                response.employee_id
            );

            $('#view_address').html(response.address);
            $('#view_birthdate').html(response.birthdate);
            $('#view_contact').html(response.contact_info);
            $('#view_gender').html(response.gender);
            $('#view_position').html(response.position_name);
            $('#view_department').html(response.department);
            $('#view_benefits').html(response.benefits);

            $('#view_schedule').html(
                response.time_in + ' - ' + response.time_out
            );
        }
    });
});

/* =========================================================
   EDIT FUNCTION
========================================================= */

function getRow(id){

    $.ajax({

        type:'POST',

        url:'employee_row.php',

        data:{id:id},

        dataType:'json',

        success:function(response){

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

/* =========================================================
   DEPARTMENT FILTER
========================================================= */

$(document).ready(function(){

    setTimeout(function(){

        var filterHTML = `

            <select id="deptFilter"
                    class="form-control input-sm">

                <option value="all">
                    All Departments
                </option>

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

/* =========================================================
   FILTER LOGIC
========================================================= */

$(document).on('change', '#deptFilter', function(){

    var selected = $(this).val();

    $('.employee-row').each(function(){

        var dept = $(this).data('dept');

        if(selected === 'all'){

            $(this).show();
        }
        else{

            (dept === selected)
            ?
            $(this).show()
            :
            $(this).hide();
        }

    });

});

</script>

<?php include 'includes/datatable_initializer.php'; ?>
<script>

$(document).on('change', '#position_name', function(){

    var position_name = $(this).val();

    $.ajax({

        url: 'ajax_position_salary.php',
        type: 'POST',

        data: {
            position_name: position_name
        },

        success: function(data){

            $('#salary_package').html(data);

        }

    });

});


</script>

</body>
</html>
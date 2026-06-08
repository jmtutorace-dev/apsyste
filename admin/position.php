<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<style>

.content-header h1{
    font-weight:700;
}

.box{
    border:none;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.salary-badge{
    display:inline-block;
    margin:2px;
    padding:5px 10px;
    border-radius:20px;
    background:#eef5ff;
    color:#1e5aa8;
    font-size:12px;
    font-weight:600;
}

#example1 tbody tr:hover{
    background:#f7fbff;
}

#example1 td{
    vertical-align:middle !important;
}

</style>

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

       <i class="fa fa-plus"></i> New Position

    </a>

</div>

<div class="box-body">

<table id="example1" class="table table-bordered">

<thead>
<tr>

    <th width="25%">Position Title</th>
    <th width="55%">Salary Packages</th>
    <th width="20%">Tools</th>

</tr>
</thead>

<tbody>

<?php

$sql = "
SELECT
    description,

    GROUP_CONCAT(
        rate
        ORDER BY rate ASC
        SEPARATOR '|'
    ) AS salary_packages

FROM position

GROUP BY description

ORDER BY description ASC
";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

?>

<tr>

    <td>
        <strong>
            <?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>
        </strong>
    </td>

    <td>

        <?php

        $packages = explode('|', $row['salary_packages']);

        foreach($packages as $salary){

            echo "
            <span class='salary-badge'>
                ₱".number_format($salary,2)."
            </span>
            ";
        }

        ?>

    </td>

    <td>

        <button
            class="btn btn-info btn-sm"
            onclick="window.location='position_packages.php?position=<?php echo urlencode($row['description']); ?>'">

            <i class="fa fa-cog"></i>
            Manage Packages

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
<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
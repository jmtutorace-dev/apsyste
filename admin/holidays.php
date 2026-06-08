<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<style>

body{
    background:#f4f6f9;
}

/* PAGE TITLE */
.page-title{
    font-size:28px;
    font-weight:700;
    color:#183b56;
    margin:0;
}

/* STAT CARDS */
.stat-card{
    background:#fff;
    border-radius:16px;
    padding:25px;
    text-align:center;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.stat-card .number{
    font-size:34px;
    font-weight:700;
    color:#183b56; 
    position: absolute;
}

.stat-card .text{
    color:#64748b;
    font-size:14px;
    margin-top:5px;
}

/* MAIN CARD */
.holiday-card{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

/* HEADER */
.card-header-custom{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.card-header-custom h4{
    margin:0;
    font-size:20px;
    font-weight:700;
    color:#183b56;
}

/* SEARCH */
.search-box{
    margin-bottom:20px;
}

/* TABLE */
.table{
    margin-bottom:0;
}

.table thead th{
    background:#f8fafc;
    border:none !important;
    color:#64748b;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.table tbody td{
    vertical-align:middle;
    border-top:1px solid #eef2f7 !important;
}

.table tbody tr:hover{
    background:#fafcff;
}

/* ✅ SCROLL CONTAINER (NEW FIX) */
.table-scroll{
    max-height: 420px;
    overflow-y: auto;
    border-radius: 10px;
}

/* LABELS */
.label{
    padding:6px 10px;
    border-radius:20px;
    font-size:11px;
}

.label-danger{
    background:#ef4444 !important;
}

.label-warning{
    background:#f59e0b !important;
}

/* BUTTONS */
.btn-primary{
    border-radius:8px;
}

.btn-danger{
    border-radius:6px;
}

/* MOBILE */
@media(max-width:768px){

    .card-header-custom{
        flex-direction:column;
        gap:10px;
        align-items:flex-start;
    }

}

</style>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">

    <h1 class="page-title">
        Holiday Management
    </h1>

</section>

<section class="content">

<?php

$total = $conn->query("SELECT COUNT(*) total FROM holidays")
              ->fetch_assoc()['total'];

$regular = $conn->query("SELECT COUNT(*) total FROM holidays WHERE type='Regular'")
                ->fetch_assoc()['total'];

$special = $conn->query("SELECT COUNT(*) total FROM holidays WHERE type='Special'")
                ->fetch_assoc()['total'];

?>

<div class="row">

    <div class="col-md-4">
        <div class="stat-card">
            <div class="number"><?php echo $total; ?></div>
            <div class="text">Total Holidays</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="number"><?php echo $regular; ?></div>
            <div class="text">Regular Holidays</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="number"><?php echo $special; ?></div>
            <div class="text">Special Holidays</div>
        </div>
    </div>

</div>

<div class="holiday-card">

    <div class="card-header-custom">

        <h4>Holiday List</h4>

        <button class="btn btn-primary"
                data-toggle="modal"
                data-target="#addHoliday">

            <i class="fa fa-plus"></i>
            Add Holiday

        </button>

    </div>

    <div class="search-box">
        <input type="text"
               id="holidaySearch"
               class="form-control"
               placeholder="Search holiday...">
    </div>

    <!-- ✅ SCROLL WRAPPER ADDED HERE -->
    <div class="table-scroll">

        <div class="table-responsive">

            <table class="table table-hover" id="holidayTable">

                <thead>
                <tr>
                    <th>Date</th>
                    <th>Holiday</th>
                    <th>Type</th>
                    <th width="80">Action</th>
                </tr>
                </thead>

                <tbody>

                <?php
                $sql = "SELECT * FROM holidays ORDER BY holiday_date ASC";
                $query = $conn->query($sql);

                while($row = $query->fetch_assoc()){

                    $label = strtolower($row['type']) == 'regular'
                        ? 'label-danger'
                        : 'label-warning';
                ?>

                <tr>

                    <td><?php echo date('F d, Y', strtotime($row['holiday_date'])); ?></td>

                    <td><strong><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></strong></td>

                    <td>
                        <span class="label <?php echo $label; ?>">
                            <?php echo htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </td>

                    <td>
                        <a href="holiday_delete.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-danger btn-xs"
                           onclick="return confirm('Delete this holiday?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</section>

</div>

<!-- ADD HOLIDAY MODAL -->
<div class="modal fade" id="addHoliday">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="holiday_add.php">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h4 class="modal-title">Add Holiday</h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="Regular">Regular Holiday</option>
                            <option value="Special">Special Holiday</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="add" class="btn btn-primary">
                        Save Holiday
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

<script>

$(document).ready(function(){

    $('#holidaySearch').on('keyup', function(){

        var value = $(this).val().toLowerCase();

        $('#holidayTable tbody tr').filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });

    });

});

</script>

</body>
</html>
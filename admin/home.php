<?php include 'includes/session.php'; ?>
<?php 
  include '../timezone.php'; 

  $today = date('Y-m-d');
  $year = date('Y');

  if(isset($_GET['year'])){
    $year = $_GET['year'];
  }
?>

<?php include 'includes/header.php'; ?>

<style>

/* =========================================================
   DASHBOARD SPACING FIX
========================================================= */

.content-header{
    padding:14px 18px 8px 18px !important;
}

.content{
    padding:8px 18px 18px 18px !important;
}

/* =========================================================
   SMALL BOXES
========================================================= */

.small-box{

    border-radius:18px;

    overflow:hidden;

    box-shadow:
        0 4px 14px rgba(0,0,0,0.05);

    margin-bottom:18px !important;

    transition:.18s;
}

.small-box:hover{

    transform:translateY(-2px);
}

.small-box .inner{

    padding:18px;
}

.small-box h3{

    font-size:28px !important;

    font-weight:700;

    margin:0 0 5px 0;
}

.small-box p{

    font-size:13px;

    margin:0;
}

.small-box .icon{

    top:8px !important;

    right:12px !important;

    font-size:60px !important;

    opacity:.12;
}

.small-box-footer{

    padding:8px 12px !important;

    font-size:12px;
}

/* =========================================================
   BOX
========================================================= */

.box{

    border:none !important;

    border-radius:18px;

    overflow:hidden;

    box-shadow:
        0 5px 16px rgba(0,0,0,0.05);
}

.box-header{

    padding:16px 18px !important;

    border-bottom:1px solid #eef2f7;
}

.box-title{

    font-size:17px;

    font-weight:700;
}

/* =========================================================
   YEAR SELECT
========================================================= */

#select_year{

    border-radius:10px;

    height:34px;

    padding:4px 10px;

    border:1px solid #d9e2ec;
}

/* =========================================================
   CHART AREA
========================================================= */

.chart{

    padding:5px 10px 12px 10px;
}

#barChart{

    max-height:320px !important;
}

/* =========================================================
   LEGEND
========================================================= */

#legend{

    margin-bottom:8px;
}

#legend ul{

    padding:0;

    margin:0;
}

#legend ul li{

    display:inline-block;

    margin:0 5px;

    padding:6px 12px 6px 28px;

    border-radius:20px;

    background:#f8fbff;

    font-size:12px;

    position:relative;
}

#legend li span{

    position:absolute;

    left:10px;

    top:50%;

    transform:translateY(-50%);

    width:10px;

    height:10px;

    border-radius:50%;
}

/* =========================================================
   ALERTS
========================================================= */

.alert{

    border:none;

    border-radius:12px;

    box-shadow:
        0 3px 10px rgba(0,0,0,0.04);
}

/* =========================================================
   MOBILE
========================================================= */

@media(max-width:768px){

    .small-box h3{

        font-size:22px !important;
    }

    .small-box .icon{

        font-size:48px !important;
    }

    .content-header h1{

        font-size:22px;
    }
}

</style>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">

        <!-- Content Header -->
        <section class="content-header">

            <h1>
                Dashboard
            </h1>

            <ol class="breadcrumb">

                <li>
                    <a href="#">
                        <i class="fa fa-dashboard"></i> Home
                    </a>
                </li>

                <li class="active">
                    Dashboard
                </li>

            </ol>

        </section>

        <!-- Main content -->
        <section class="content">

        <?php

        if(isset($_SESSION['error'])){

          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button'
                      class='close'
                      data-dismiss='alert'
                      aria-hidden='true'>

                      &times;

              </button>

              ".$_SESSION['error']."
            </div>
          ";

          unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])){

          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button'
                      class='close'
                      data-dismiss='alert'
                      aria-hidden='true'>

                      &times;

              </button>

              ".$_SESSION['success']."
            </div>
          ";

          unset($_SESSION['success']);
        }

        ?>

        <!-- SMALL BOXES -->
        <div class="row">

            <!-- EMPLOYEES -->
            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-aqua">

                    <div class="inner">

                        <?php

                        $sql = "SELECT * FROM employees";
                        $query = $conn->query($sql);

                        echo "<h3>".$query->num_rows."</h3>";

                        ?>

                        <p>Total Employees</p>

                    </div>

                    <div class="icon">
                        <i class="ion ion-person-stalker"></i>
                    </div>

                    <a href="employee.php"
                       class="small-box-footer">

                       More info
                       <i class="fa fa-arrow-circle-right"></i>

                    </a>

                </div>

            </div>

            <!-- ON TIME % -->
            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-green">

                    <div class="inner">

                        <?php

                        $sql = "SELECT * FROM attendance";
                        $query = $conn->query($sql);

                        $total = $query->num_rows;

                        $sql = "SELECT * FROM attendance WHERE status = 1";
                        $query = $conn->query($sql);

                        $early = $query->num_rows;

                        $percentage = ($total > 0)
                                      ? ($early/$total)*100
                                      : 0;

                        echo "<h3>"
                              .number_format($percentage,2).
                              "<sup style='font-size:16px'>%</sup>
                              </h3>";

                        ?>

                        <p>On Time Percentage</p>

                    </div>

                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>

                    <a href="attendance.php"
                       class="small-box-footer">

                       More info
                       <i class="fa fa-arrow-circle-right"></i>

                    </a>

                </div>

            </div>

            <!-- ON TIME TODAY -->
            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-yellow">

                    <div class="inner">

                        <?php

                        $sql = "SELECT * FROM attendance
                                WHERE date = '$today'
                                AND status = 1";

                        $query = $conn->query($sql);

                        echo "<h3>".$query->num_rows."</h3>";

                        ?>

                        <p>On Time Today</p>

                    </div>

                    <div class="icon">
                        <i class="ion ion-clock"></i>
                    </div>

                    <a href="attendance.php"
                       class="small-box-footer">

                       More info
                       <i class="fa fa-arrow-circle-right"></i>

                    </a>

                </div>

            </div>

            <!-- LATE TODAY -->
            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">

                    <div class="inner">

                        <?php

                        $sql = "SELECT * FROM attendance
                                WHERE date = '$today'
                                AND status = 0";

                        $query = $conn->query($sql);

                        echo "<h3>".$query->num_rows."</h3>";

                        ?>

                        <p>Late Today</p>

                    </div>

                    <div class="icon">
                        <i class="ion ion-alert-circled"></i>
                    </div>

                    <a href="attendance.php"
                       class="small-box-footer">

                       More info
                       <i class="fa fa-arrow-circle-right"></i>

                    </a>

                </div>

            </div>

        </div>

        <!-- CHART -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">
                            Monthly Attendance Report
                        </h3>

                        <div class="box-tools pull-right">

                            <form class="form-inline">

                                <div class="form-group">

                                    <label>
                                        Select Year:
                                    </label>

                                    <select class="form-control input-sm"
                                            id="select_year">

                                        <?php

                                        for($i=2015; $i<=2065; $i++){

                                            $selected = ($i==$year)
                                                        ? 'selected'
                                                        : '';

                                            echo "
                                            <option value='".$i."' ".$selected.">
                                                ".$i."
                                            </option>
                                            ";
                                        }

                                        ?>

                                    </select>

                                </div>

                            </form>

                        </div>

                    </div>

                    <div class="box-body">

                        <div class="chart">

                            <div id="legend"
                                 class="text-center">
                            </div>

                            <canvas id="barChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        </section>

    </div>

    <?php include 'includes/footer.php'; ?>

</div>

<?php

$and = 'AND YEAR(date) = '.$year;

$months = array();
$ontime = array();
$late = array();

for($m = 1; $m <= 12; $m++){

    $sql = "SELECT * FROM attendance
            WHERE MONTH(date) = '$m'
            AND status = 1 $and";

    $oquery = $conn->query($sql);

    array_push($ontime, $oquery->num_rows);

    $sql = "SELECT * FROM attendance
            WHERE MONTH(date) = '$m'
            AND status = 0 $and";

    $lquery = $conn->query($sql);

    array_push($late, $lquery->num_rows);

    $month = date(
        'M',
        mktime(0,0,0,$m,1)
    );

    array_push($months, $month);
}

$months = json_encode($months);
$late = json_encode($late);
$ontime = json_encode($ontime);

?>

<?php include 'includes/scripts.php'; ?>

<script>

$(function(){

    var barChartCanvas =
        $('#barChart')
        .get(0)
        .getContext('2d');

    var barChart = new Chart(barChartCanvas);

    var barChartData = {

        labels : <?php echo $months; ?>,

        datasets: [

            {

                label : 'Late',

                fillColor : '#ef4444',

                strokeColor : '#ef4444',

                pointColor : '#ef4444',

                data : <?php echo $late; ?>

            },

            {

                label : 'Ontime',

                fillColor : '#22c55e',

                strokeColor : '#22c55e',

                pointColor : '#22c55e',

                data : <?php echo $ontime; ?>

            }

        ]
    };

    var barChartOptions = {

        scaleBeginAtZero : true,

        responsive : true,

        maintainAspectRatio : false,

        barValueSpacing : 4,

        scaleGridLineColor : 'rgba(0,0,0,.04)',

        legendTemplate :

        '<ul class="<%=name.toLowerCase()%>-legend">'+
        '<% for (var i=0; i<datasets.length; i++){%>'+
        '<li>'+
        '<span style="background-color:<%=datasets[i].fillColor%>"></span>'+
        '<%=datasets[i].label%>'+
        '</li>'+
        '<%}%>'+
        '</ul>'

    };

    var myChart =
        barChart.Bar(
            barChartData,
            barChartOptions
        );

    document.getElementById('legend').innerHTML =
        myChart.generateLegend();

});

</script>

<script>

$(function(){

    $('#select_year').change(function(){

        window.location.href =
            'home.php?year=' + $(this).val();

    });

});

</script>

</body>
</html>
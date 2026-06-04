<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
    <h1>13th Month Pay</h1>
</section>

<section class="content">

<div class="box">

<div class="box-header with-border">
    <h3 class="box-title">
        Employee 13th Month Computation
    </h3>
</div>

<div class="box-body">

<table id="example1"
       class="table table-bordered table-striped">

<thead>

<tr>

    <th>Employee ID</th>
    <th>Employee Name</th>
    <th>Date Hired</th>
    <th>Monthly Salary</th>
    <th>Months Worked</th>
    <th>Total Basic Salary Earned</th>
    <th>13th Month Pay</th>
    <th>Released</th>
<th>Balance</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php

$currentYear = date('Y');

$sql = "
SELECT
    employees.*,
    position.rate
FROM employees
LEFT JOIN position
ON position.id = employees.position_id
ORDER BY lastname ASC
";

$query = $conn->query($sql);

while($row = $query->fetch_assoc()){

    $empid = $row['id'];

    $monthlySalary =
        ($row['rate'] > 0)
        ? $row['rate']
        : 0;

    $dateHired = $row['created_on'];

    $hireYear = date(
        'Y',
        strtotime($dateHired)
    );

    if($hireYear < $currentYear){

        $monthsWorked = 12;
    }
    else{

        $hireMonth = date(
            'n',
            strtotime($dateHired)
        );

        $monthsWorked =
            (12 - $hireMonth) + 1;

        if($monthsWorked < 0){
            $monthsWorked = 0;
        }
    }

    $totalBasicSalaryEarned =
        $monthlySalary *
        $monthsWorked;

    $thirteenthMonth =
        $totalBasicSalaryEarned / 12;

    // Released Amount

    $relsql = "
    SELECT
        SUM(amount) AS released
    FROM thirteenth_month_release
    WHERE employee_id = '$empid'
    ";

    $relquery = $conn->query($relsql);

    $relrow = $relquery->fetch_assoc();

    $released =
        !empty($relrow['released'])
        ? $relrow['released']
        : 0;

    $balance =
        $thirteenthMonth - $released;

    echo "
    <tr>

        <td>".$row['employee_id']."</td>

        <td>
            ".$row['lastname'].",
            ".$row['firstname']."
        </td>

        <td>
            ".date(
                'M d, Y',
                strtotime($dateHired)
            )."
        </td>

        <td>
            ₱".number_format(
                $monthlySalary,
                2
            )."
        </td>

        <td>
            ".$monthsWorked."
        </td>

        <td>
            ₱".number_format(
                $totalBasicSalaryEarned,
                2
            )."
        </td>

        <td>
            <b>₱".number_format(
                $thirteenthMonth,
                2
            )."</b>
        </td>

        <td>
            ₱".number_format(
                $released,
                2
            )."
        </td>

        <td>
            <b>
            ₱".number_format(
                $balance,
                2
            )."
            </b>
        </td>
        <td>
    <a href='thirteenth_month_release.php?id=".$empid."&type=Midyear'
       class='btn btn-primary btn-sm'
       onclick=\"return confirm('Release Midyear 13th Month Pay?')\">

        Release Midyear

    </a>

    <a href='thirteenth_month_release.php?id=".$empid."&type=Year End'
       class='btn btn-success btn-sm'
       onclick=\"return confirm('Release Year End 13th Month Pay?')\">

        Release Year End

    </a>
    

    <a href='thirteenth_month_history.php?id=".$empid."'
       class='btn btn-info btn-sm'>

        View History

    </a>

    <a href='thirteenth_month_pdf.php?id=".$empid."'
       class='btn btn-danger btn-sm'
       target='_blank'>

        PDF

    </a>

</td>

    </tr>
    ";
}

?>

</tbody>

</table>

</div>

</div>

</section>

</div>

<?php include 'includes/footer.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>
<?php include 'includes/datatable_initializer.php'; ?>

</body>
</html>
<?php
include 'includes/session.php';

function compute_tax($salary){

    $tax = 0;

    if ($salary <= 10417) {

        $tax = 0;

    } elseif ($salary <= 16666) {

        $tax = ($salary - 10417) * 0.15;

    } elseif ($salary <= 33332) {

        $tax = 937.50 + (($salary - 16667) * 0.20);

    } elseif ($salary <= 83332) {

        $tax = 4270.70 + (($salary - 33333) * 0.25);

    } elseif ($salary <= 333332) {

        $tax = 16770.70 + (($salary - 83333) * 0.30);

    } else {

        $tax = 91770.70 + (($salary - 333333) * 0.35);
    }

    return round($tax, 2);
}

function generateRow($from, $to, $conn){

	$contents = '';

	$sql = "SELECT *,
				   attendance.employee_id AS empid

			FROM attendance

			LEFT JOIN employees
				ON employees.id = attendance.employee_id

			LEFT JOIN position
				ON position.id = employees.position_id

			LEFT JOIN schedules
				ON schedules.id = employees.schedule_id

			WHERE date BETWEEN '$from' AND '$to'

			GROUP BY attendance.employee_id

			ORDER BY employees.lastname ASC,
					 employees.firstname ASC";

	$query = $conn->query($sql);

	$total = 0;

	while($row = $query->fetch_assoc()){

		$empid = $row['empid'];

		// =====================================================
		// MONTHLY SALARY
		// =====================================================

		$monthly_salary = $row['rate']
						 ? $row['rate']
						 : 0;

		// =====================================================
		// DAILY & HOURLY RATE
		// =====================================================

		$daily_rate = $monthly_salary / 26;

		$hourly_rate = $daily_rate / 8;

		// =====================================================
		// ATTENDANCE
		// =====================================================

		$days_present = 0;
		$total_worked_hours = 0;
		$late_deduction = 0;

		$attsql = "SELECT *
				   FROM attendance
				   WHERE employee_id='$empid'
				   AND date BETWEEN '$from' AND '$to'";

		$attquery = $conn->query($attsql);

		while($attrow = $attquery->fetch_assoc()){

			$worked_hours = $attrow['num_hr'];

			if($worked_hours <= 0){
				continue;
			}

			$days_present++;

			$total_worked_hours += $worked_hours;

			// =====================================================
			// LATE / UNDERTIME
			// =====================================================

			if($worked_hours < 8){

				$undertime_hours = 8 - $worked_hours;

				$late_deduction += (
					$undertime_hours * $hourly_rate
				);
			}
		}

		// =====================================================
		// BASE PAY
		// =====================================================

		$base_pay = (
			$total_worked_hours * $hourly_rate
		);

		// =====================================================
		// OVERTIME
		// =====================================================

		$otsql = "SELECT SUM(hours * rate) AS overtime_pay
				  FROM overtime
				  WHERE employee_id='$empid'
				  AND date_overtime BETWEEN '$from' AND '$to'";

		$otquery = $conn->query($otsql);

		$otrow = $otquery->fetch_assoc();

		$overtime = $otrow['overtime_pay']
				   ? $otrow['overtime_pay']
				   : 0;

		// =====================================================
		// CASH ADVANCE
		// =====================================================

		$casql = "SELECT SUM(amount) AS cashamount
				  FROM cashadvance
				  WHERE employee_id='$empid'
				  AND date_advance BETWEEN '$from' AND '$to'";

		$caquery = $conn->query($casql);

		$carow = $caquery->fetch_assoc();

		$cashadvance = $carow['cashamount']
					  ? $carow['cashamount']
					  : 0;

		// =====================================================
		// GOVERNMENT DEDUCTIONS
		// =====================================================

		$government_deduction = 0;

		$dsql = "SELECT deductions.*
				 FROM employee_deductions

				 LEFT JOIN deductions
				 ON deductions.id = employee_deductions.deduction_id

				 WHERE employee_deductions.employee_id = '$empid'";

		$dquery = $conn->query($dsql);

		while($drow = $dquery->fetch_assoc()){

			$description = strtolower(trim($drow['description']));

			// SKIP TAX HERE
			if(
				$description == 'tax' ||
				$description == 'withholding tax'
			){
				continue;
			}

			$amount = $drow['amount'];

			$type = strtolower($drow['type']);

			// PERCENTAGE
			if(
				$type == 'percent' ||
				$type == 'percentage'
			){

				$computed =
					$monthly_salary * ($amount / 100);
			}

			// FIXED
			else{

				$computed = $amount;
			}

			// SEMI MONTHLY
			$computed = $computed / 2;

			$government_deduction += $computed;
		}

		// =====================================================
		// TAX
		// =====================================================

		$tax = compute_tax($monthly_salary);

		// =====================================================
		// GROSS PAY
		// =====================================================

		$gross = $base_pay + $overtime;

		// =====================================================
		// TOTAL DEDUCTIONS
		// =====================================================

		$total_deduction =
			$government_deduction
			+ $tax
			+ $cashadvance
			+ $late_deduction;

		// =====================================================
		// NET PAY
		// =====================================================

		$net = $gross - $total_deduction;

		$total += $net;

		$contents .= "
			<tr>

				<td>
					{$row['lastname']}, {$row['firstname']}
				</td>

				<td align='center'>
					{$row['employee_id']}
				</td>

				<td align='right'>
					".number_format($monthly_salary, 2)."
				</td>

				<td align='center'>
					".$days_present."
				</td>

				<td align='right'>
					".number_format($total_worked_hours, 2)."
				</td>

				<td align='right'>
					".number_format($base_pay, 2)."
				</td>

				<td align='right'>
					".number_format($overtime, 2)."
				</td>

				<td align='right'>
					".number_format($late_deduction, 2)."
				</td>

				<td align='right'>
					".number_format($government_deduction, 2)."
				</td>

				<td align='right'>
					".number_format($tax, 2)."
				</td>

				<td align='right'>
					".number_format($cashadvance, 2)."
				</td>

				<td align='right'>
					<b>".number_format($net, 2)."</b>
				</td>

			</tr>
		";
	}

	$contents .= "
		<tr>

			<td colspan='11' align='right'>
				<b>Total Net Pay</b>
			</td>

			<td align='right'>
				<b>".number_format($total, 2)."</b>
			</td>

		</tr>
	";

	return $contents;
}

$range = $_POST['date_range'];

$ex = explode(' - ', $range);

$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

// =====================================================
// DATE TITLE
// =====================================================

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

// =====================================================
// TCPDF
// =====================================================

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);

$pdf->SetTitle('Payroll: '.$from_title.' - '.$to_title);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10);

$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

// =====================================================
// PDF CONTENT
// =====================================================

$content = '';

$content .= '
<h2 align="center">
	TechSoft IT Solutions
</h2>

<h4 align="center">
	Payroll Summary
</h4>

<h4 align="center">
	'.$from_title.' - '.$to_title.'
</h4>

<table border="1" cellspacing="0" cellpadding="4">

	<tr>

		<th width="16%" align="center">
			<b>Employee Name</b>
		</th>

		<th width="8%" align="center">
			<b>ID</b>
		</th>

		<th width="10%" align="center">
			<b>Monthly</b>
		</th>

		<th width="6%" align="center">
			<b>Days</b>
		</th>

		<th width="8%" align="center">
			<b>Hours</b>
		</th>

		<th width="10%" align="center">
			<b>Base Pay</b>
		</th>

		<th width="8%" align="center">
			<b>OT</b>
		</th>

		<th width="10%" align="center">
			<b>Late/UT</b>
		</th>

		<th width="10%" align="center">
			<b>Gov Ded.</b>
		</th>

		<th width="8%" align="center">
			<b>Tax</b>
		</th>

		<th width="8%" align="center">
			<b>Cash Adv.</b>
		</th>

		<th width="10%" align="center">
			<b>Net Pay</b>
		</th>

	</tr>
';

$content .= generateRow($from, $to, $conn);

$content .= '</table>';

$pdf->writeHTML($content);

$pdf->Output('payroll.pdf', 'I');
?>
<?php
	include 'includes/session.php';

	function generateRow($from, $to, $conn, $deduction){

		$contents = '';
	 	
		$sql = "SELECT *,
					   SUM(num_hr) AS total_hr,
					   attendance.employee_id AS empid
				FROM attendance
				LEFT JOIN employees
					ON employees.id=attendance.employee_id
				LEFT JOIN position
					ON position.id=employees.position_id
				WHERE date BETWEEN '$from' AND '$to'
				GROUP BY attendance.employee_id
				ORDER BY employees.lastname ASC, employees.firstname ASC";

		$query = $conn->query($sql);

		$total = 0;

		while($row = $query->fetch_assoc()){

			$empid = $row['empid'];

	      	// Cash Advance
	      	$casql = "SELECT SUM(amount) AS cashamount
					  FROM cashadvance
					  WHERE employee_id='$empid'
					  AND date_advance BETWEEN '$from' AND '$to'";

	      	$caquery = $conn->query($casql);
	      	$carow = $caquery->fetch_assoc();

	      	$cashadvance = $carow['cashamount'] ? $carow['cashamount'] : 0;

			// Overtime
			$otsql = "SELECT SUM(hours * rate) AS overtime_pay
					  FROM overtime
					  WHERE employee_id='$empid'
					  AND date_overtime BETWEEN '$from' AND '$to'";

			$otquery = $conn->query($otsql);
			$otrow = $otquery->fetch_assoc();

			$overtime = $otrow['overtime_pay'] ? $otrow['overtime_pay'] : 0;

			// Regular Pay
			$regular_pay = $row['rate'] * $row['total_hr'];

			// Gross
			$gross = $regular_pay + $overtime;

			// Deductions
			$total_deduction = $deduction + $cashadvance;

			// Net
      		$net = $gross - $total_deduction;

			$total += $net;

			$contents .= '
			<tr>
				<td>'.$row['lastname'].', '.$row['firstname'].'</td>
				<td>'.$row['employee_id'].'</td>
				<td align="right">'.number_format($gross, 2).'</td>
				<td align="right">'.number_format($net, 2).'</td>
			</tr>
			';
		}

		$contents .= '
			<tr>
				<td colspan="3" align="right"><b>Total</b></td>
				<td align="right"><b>'.number_format($total, 2).'</b></td>
			</tr>
		';

		return $contents;
	}
		
	$range = $_POST['date_range'];

	$ex = explode(' - ', $range);

	$from = date('Y-m-d', strtotime($ex[0]));
	$to = date('Y-m-d', strtotime($ex[1]));

	// Deductions
	$sql = "SELECT SUM(amount) as total_amount FROM deductions";

    $query = $conn->query($sql);

   	$drow = $query->fetch_assoc();

    $deduction = $drow['total_amount'] ? $drow['total_amount'] : 0;

	$from_title = date('M d, Y', strtotime($ex[0]));
	$to_title = date('M d, Y', strtotime($ex[1]));

	require_once('../tcpdf/tcpdf.php');

    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);

    $pdf->SetTitle('Payroll: '.$from_title.' - '.$to_title);

    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont('helvetica');

    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);

    $pdf->setPrintHeader(false);

    $pdf->setPrintFooter(false);

    $pdf->SetAutoPageBreak(TRUE, 10);

    $pdf->SetFont('helvetica', '', 11);

    $pdf->AddPage();

    $content = '';

    $content .= '
      	<h2 align="center">TechSoft IT Solutions</h2>
      	<h4 align="center">'.$from_title." - ".$to_title.'</h4>

      	<table border="1" cellspacing="0" cellpadding="3">

           <tr>
           		<th width="35%" align="center"><b>Employee Name</b></th>
                <th width="20%" align="center"><b>Employee ID</b></th>
				<th width="20%" align="center"><b>Gross Pay</b></th>
				<th width="25%" align="center"><b>Net Pay</b></th>
           </tr>
      ';

    $content .= generateRow($from, $to, $conn, $deduction);

    $content .= '</table>';

    $pdf->writeHTML($content);

    $pdf->Output('payroll.pdf', 'I');
?>
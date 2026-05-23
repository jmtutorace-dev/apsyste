<?php
	include 'includes/session.php';
	
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
    $pdf->SetTitle('Payslip: '.$from_title.' - '.$to_title);

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

    $contents = '';

	$sql = "SELECT *,
				   SUM(num_hr) AS total_hr,
				   attendance.employee_id AS empid,
				   employees.employee_id AS employee
			FROM attendance
			LEFT JOIN employees
				ON employees.id=attendance.employee_id
			LEFT JOIN position
				ON position.id=employees.position_id
			WHERE date BETWEEN '$from' AND '$to'
			GROUP BY attendance.employee_id
			ORDER BY employees.lastname ASC, employees.firstname ASC";

	$query = $conn->query($sql);

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

		// Gross Pay
		$gross = $regular_pay + $overtime;

		// Total Deduction
		$total_deduction = $deduction + $cashadvance;

		// Net Pay
  		$net = $gross - $total_deduction;

		$contents .= '
			<h2 align="center">TechSoft IT Solutions</h2>
			<h4 align="center">'.$from_title." - ".$to_title.'</h4>

			<table cellspacing="0" cellpadding="3">

    	       	<tr>
            		<td width="25%" align="right">Employee Name: </td>
                 	<td width="25%"><b>'.$row['firstname']." ".$row['lastname'].'</b></td>

				 	<td width="25%" align="right">Rate per Hour: </td>
                 	<td width="25%" align="right">'.number_format($row['rate'], 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td width="25%" align="right">Employee ID: </td>
				 	<td width="25%">'.$row['employee'].'</td>

				 	<td width="25%" align="right">Total Hours: </td>
				 	<td width="25%" align="right">'.number_format($row['total_hr'], 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right">Regular Pay:</td>
				 	<td width="25%" align="right">'.number_format($regular_pay, 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right">Overtime Pay:</td>
				 	<td width="25%" align="right">'.number_format($overtime, 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right"><b>Gross Pay:</b></td>
				 	<td width="25%" align="right"><b>'.number_format($gross, 2).'</b></td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right">Deduction:</td>
				 	<td width="25%" align="right">'.number_format($deduction, 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right">Cash Advance:</td>
				 	<td width="25%" align="right">'.number_format($cashadvance, 2).'</td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right"><b>Total Deduction:</b></td>
				 	<td width="25%" align="right"><b>'.number_format($total_deduction, 2).'</b></td>
    	    	</tr>

    	    	<tr>
    	    		<td></td>
    	    		<td></td>

				 	<td width="25%" align="right"><b>Net Pay:</b></td>
				 	<td width="25%" align="right"><b>'.number_format($net, 2).'</b></td>
    	    	</tr>

    	    </table>

    	    <br><hr>
		';
	}

    $pdf->writeHTML($contents);

    $pdf->Output('payslip.pdf', 'I');
?>
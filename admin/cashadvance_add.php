<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$employee = trim($_POST['employee']);
		$amount   = $_POST['amount'];

		$lookup = $conn->prepare("SELECT id FROM employees WHERE employee_id = ?");
		$lookup->bind_param('s', $employee);
		$lookup->execute();
		$res = $lookup->get_result();

		if($res->num_rows < 1){
			$_SESSION['error'] = 'Employee not found';
		}else{
			$employee_id = (int) $res->fetch_assoc()['id'];

			$stmt = $conn->prepare("INSERT INTO cashadvance (employee_id, date_advance, amount) VALUES (?, NOW(), ?)");
			$stmt->bind_param('id', $employee_id, $amount);
			if($stmt->execute()){
				$_SESSION['success'] = 'Cash Advance added successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: cashadvance.php');
	exit();
<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$description = trim($_POST['description']);
		$amount      = $_POST['amount'];
		$type        = trim($_POST['type']);

		if(!is_numeric($amount)){
			$_SESSION['error'] = 'Amount must be a number.';
		}else{
			$stmt = $conn->prepare("INSERT INTO deductions (description, amount, type) VALUES (?, ?, ?)");
			$stmt->bind_param('sds', $description, $amount, $type);
			if($stmt->execute()){
				$_SESSION['success'] = 'Deduction added successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: deduction.php');
	exit();
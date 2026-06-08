<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id          = intval($_POST['id']);
		$description = trim($_POST['description']);
		$amount      = $_POST['amount'];
		$type        = trim($_POST['type']);

		if(!is_numeric($amount)){
			$_SESSION['error'] = 'Amount must be a number.';
		}else{
			$stmt = $conn->prepare("UPDATE deductions SET description = ?, amount = ?, type = ? WHERE id = ?");
			$stmt->bind_param('sdsi', $description, $amount, $type, $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Deduction updated successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: deduction.php');
	exit();

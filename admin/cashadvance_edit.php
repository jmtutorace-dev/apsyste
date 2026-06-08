<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id     = intval($_POST['id']);
		$amount = $_POST['amount'];

		$stmt = $conn->prepare("UPDATE cashadvance SET amount = ? WHERE id = ?");
		$stmt->bind_param('di', $amount, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Cash advance updated successfully';
		}else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:cashadvance.php');
	exit();
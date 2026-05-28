<?php

	include 'includes/session.php';

	if(isset($_POST['edit'])){

		$id = $_POST['id'];

		$description = $_POST['description'];

		$amount = $_POST['amount'];

		$type = $_POST['type'];

		$sql = "UPDATE deductions 
				SET description = '$description',
				    amount = '$amount',
				    type = '$type'
				WHERE id = '$id'";

		if($conn->query($sql)){

			$_SESSION['success'] = 'Deduction updated successfully';

		}
		else{

			$_SESSION['error'] = $conn->error;

		}
	}
	else{

		$_SESSION['error'] = 'Fill up edit form first';

	}

	header('location: deduction.php');

?>
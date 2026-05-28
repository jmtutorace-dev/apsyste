<?php

	include 'includes/session.php';

	if(isset($_POST['add'])){

		$description = $_POST['description'];

		$amount = $_POST['amount'];

		$type = $_POST['type'];

		$sql = "INSERT INTO deductions
				(description, amount, type)

				VALUES

				('$description', '$amount', '$type')";

		if($conn->query($sql)){

			$_SESSION['success'] = 'Deduction added successfully';

		}
		else{

			$_SESSION['error'] = $conn->error;

		}
	}
	else{

		$_SESSION['error'] = 'Fill up add form first';

	}

	header('location: deduction.php');

?>
<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){

		$empid = $_POST['id'];

		if(empty($empid)){
			$_SESSION['error'] = "Employee ID is missing";
			header('location: employee.php');
			exit();
		}

		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$address = $_POST['address'];
		$birthdate = $_POST['birthdate'];
		$contact = $_POST['contact'];
		$gender = $_POST['gender'];
		$position = $_POST['position'];
		$schedule = $_POST['schedule'];
		$department = $_POST['department'];

		$sql = "UPDATE employees SET 
					firstname = '$firstname',
					lastname = '$lastname',
					address = '$address',
					birthdate = '$birthdate',
					contact_info = '$contact',
					gender = '$gender',
					position_id = '$position',
					schedule_id = '$schedule',
					department = '$department'
				WHERE id = '$empid'";

		if($conn->query($sql)){
			$_SESSION['success'] = 'Employee updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		$_SESSION['error'] = 'EDIT button not triggered (name="edit" missing)';
	}

	header('location: employee.php');
?>
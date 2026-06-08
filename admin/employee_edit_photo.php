<?php
	include 'includes/session.php';
	include 'includes/upload_photo.php';

	if(isset($_POST['upload'])){

		$empid = intval($_POST['id']);

		$filename = isset($_FILES['photo']) ? save_employee_photo($_FILES['photo']) : null;

		if($filename === null){
			$_SESSION['error'] = 'Please choose a valid image file (jpg, png, gif).';
			header('location: employee.php');
			exit();
		}

		$stmt = $conn->prepare("UPDATE employees SET photo = ? WHERE id = ?");
		$stmt->bind_param('si', $filename, $empid);

		if($stmt->execute()){
			$_SESSION['success'] = 'Employee photo updated successfully';
		}
		else{
			$_SESSION['error'] = 'Could not update photo. Please try again.';
		}

	}
	else{
		$_SESSION['error'] = 'Select employee to update photo first';
	}

	header('location: employee.php');
	exit();
?>

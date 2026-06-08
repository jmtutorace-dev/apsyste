<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id    = intval($_POST['id']);
		$date  = $_POST['date'];
		$hours = $_POST['hours'] + ($_POST['mins'] / 60);
		$rate  = $_POST['rate'];

		$stmt = $conn->prepare("UPDATE overtime SET hours = ?, rate = ?, date_overtime = ? WHERE id = ?");
		$stmt->bind_param('ddsi', $hours, $rate, $date, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Overtime updated successfully';
		}else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:overtime.php');
	exit();
<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$empid    = intval($_POST['id']);
		$sched_id = intval($_POST['schedule']);

		$stmt = $conn->prepare("UPDATE employees SET schedule_id = ? WHERE id = ?");
		$stmt->bind_param('ii', $sched_id, $empid);
		if($stmt->execute()){
			$_SESSION['success'] = 'Schedule updated successfully';
		}else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Select schedule to edit first';
	}

	header('location: schedule_employee.php');
	exit();

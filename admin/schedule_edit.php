<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id       = intval($_POST['id']);
		$time_in  = date('H:i:s', strtotime($_POST['time_in']));
		$time_out = date('H:i:s', strtotime($_POST['time_out']));

		$stmt = $conn->prepare("UPDATE schedules SET time_in = ?, time_out = ? WHERE id = ?");
		$stmt->bind_param('ssi', $time_in, $time_out, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Schedule updated successfully';
		}else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:schedule.php');
	exit();
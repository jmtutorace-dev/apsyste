<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$time_in  = date('H:i:s', strtotime($_POST['time_in']));
		$time_out = date('H:i:s', strtotime($_POST['time_out']));

		$stmt = $conn->prepare("INSERT INTO schedules (time_in, time_out) VALUES (?, ?)");
		$stmt->bind_param('ss', $time_in, $time_out);
		if($stmt->execute()){
			$_SESSION['success'] = 'Schedule added successfully';
		}else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: schedule.php');
	exit();

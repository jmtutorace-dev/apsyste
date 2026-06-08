<?php
	include 'includes/session.php';

	$title = '';

	if(isset($_POST['add'])){
		$title = trim($_POST['title']);
		$rate  = $_POST['rate'];

		if($title === '' || !is_numeric($rate)){
			$_SESSION['error'] = 'Please enter a position name and a numeric rate.';
		}
		else{
			$stmt = $conn->prepare("INSERT INTO position (description, rate) VALUES (?, ?)");
			$stmt->bind_param('sd', $title, $rate);
			if($stmt->execute()){
				$_SESSION['success'] = 'Position added successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: position_packages.php?position='.urlencode($title));
	exit();
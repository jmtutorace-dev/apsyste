<?php
	include 'includes/session.php';

	$title = '';

	if(isset($_POST['edit'])){
		$id    = intval($_POST['id']);
		$title = trim($_POST['title']);
		$rate  = $_POST['rate'];

		if($title === '' || !is_numeric($rate)){
			$_SESSION['error'] = 'Please enter a position name and a numeric rate.';
		}
		else{
			$stmt = $conn->prepare("UPDATE position SET description = ?, rate = ? WHERE id = ?");
			$stmt->bind_param('sdi', $title, $rate, $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Position updated successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: position_packages.php?position='.urlencode($title));
	exit();

<?php
	session_start();
	include 'includes/conn.php';

	if(isset($_POST['login'])){
		$username = trim($_POST['username']);
		$password = $_POST['password'];

		$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if($result->num_rows < 1){
			$_SESSION['error'] = 'Cannot find account with the username';
		}
		else{
			$row = $result->fetch_assoc();
			if(password_verify($password, $row['password'])){
				session_regenerate_id(true);   // prevent session fixation
				$_SESSION['admin'] = $row['id'];
			}
			else{
				$_SESSION['error'] = 'Incorrect password';
			}
		}

	}
	else{
		$_SESSION['error'] = 'Input admin credentials first';
	}

	header('location: index.php');
	exit();

?>

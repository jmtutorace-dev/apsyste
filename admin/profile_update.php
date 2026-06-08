<?php
	include 'includes/session.php';
	include 'includes/upload_photo.php';

	// Only allow returning to a local page (no open redirect)
	$return = isset($_GET['return']) ? basename($_GET['return']) : 'home.php';
	if($return === '' ){ $return = 'home.php'; }

	if(isset($_POST['save'])){
		$curr_password = $_POST['curr_password'];
		$username      = trim($_POST['username']);
		$password      = $_POST['password'];
		$firstname     = trim($_POST['firstname']);
		$lastname      = trim($_POST['lastname']);

		if(password_verify($curr_password, $user['password'])){

			// Validated photo upload; keep existing photo if none/invalid
			$uploaded = isset($_FILES['photo']) ? save_employee_photo($_FILES['photo']) : null;
			$filename = ($uploaded !== null) ? $uploaded : $user['photo'];

			// If the posted password equals the stored hash, treat as "unchanged"
			if($password === $user['password']){
				$password = $user['password'];
			}
			else{
				$password = password_hash($password, PASSWORD_DEFAULT);
			}

			$stmt = $conn->prepare("UPDATE admin SET username = ?, password = ?, firstname = ?, lastname = ?, photo = ? WHERE id = ?");
			$stmt->bind_param('sssssi', $username, $password, $firstname, $lastname, $filename, $user['id']);

			if($stmt->execute()){
				$_SESSION['success'] = 'Admin profile updated successfully';
			}
			else{
				$_SESSION['error'] = 'Could not update profile. Please try again.';
			}

		}
		else{
			$_SESSION['error'] = 'Incorrect password';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up required details first';
	}

	header('location:'.$return);
	exit();
?>

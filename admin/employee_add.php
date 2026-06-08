<?php
	include 'includes/session.php';
	include 'includes/upload_photo.php';

	if(isset($_POST['add'])){

		$firstname    = trim($_POST['firstname']);
		$lastname     = trim($_POST['lastname']);
		$biometric_id = trim($_POST['biometric_id']);
		$address      = trim($_POST['address']);
		$birthdate    = $_POST['birthdate'];
		$contact      = trim($_POST['contact']);
		$gender       = $_POST['gender'];
		$position     = intval($_POST['position']);
		$department   = trim($_POST['department']);
		$schedule     = intval($_POST['schedule']);

		// =========================================
		// PHOTO (validated + stored under a safe name)
		// =========================================

		$filename = isset($_FILES['photo']) ? save_employee_photo($_FILES['photo']) : null;
		if($filename === null){ $filename = ''; }

		// =========================================
		// GENERATE EMPLOYEE ID (3 letters + 9 digits)
		// =========================================

		$letters = implode('', range('A', 'Z'));
		$numbers = '0123456789';

		$employee_id =
			substr(str_shuffle($letters), 0, 3).
			substr(str_shuffle($numbers), 0, 9);

		// =========================================
		// EMPLOYEE-PORTAL LOGIN (logs in with Employee ID; default password 123456)
		// =========================================

		$default_pw = password_hash('123456', PASSWORD_DEFAULT);

		// =========================================
		// INSERT EMPLOYEE
		// =========================================

		$stmt = $conn->prepare("INSERT INTO employees (
					employee_id, biometric_id, firstname, lastname, address,
					birthdate, contact_info, gender, position_id, department,
					schedule_id, photo, password, created_on
				) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())");

		$stmt->bind_param(
			'ssssssssisiss',
			$employee_id, $biometric_id, $firstname, $lastname, $address,
			$birthdate, $contact, $gender, $position, $department,
			$schedule, $filename, $default_pw
		);

		if($stmt->execute()){

			$new_employee_id = $conn->insert_id;

			// =========================================
			// SAVE SELECTED DEDUCTIONS
			// =========================================

			if(isset($_POST['deductions'])){

				$ins = $conn->prepare("INSERT INTO employee_deductions (employee_id, deduction_id, created_on) VALUES (?, ?, NOW())");

				foreach($_POST['deductions'] as $deduction_id){
					$deduction_id = intval($deduction_id);
					$ins->bind_param('ii', $new_employee_id, $deduction_id);
					$ins->execute();
				}
			}

			$_SESSION['success'] = 'Employee added successfully';
		}
		else{
			$_SESSION['error'] = 'Could not add employee. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: employee.php');
	exit();
?>

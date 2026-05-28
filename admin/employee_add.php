<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){

		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$address = $_POST['address'];
		$birthdate = $_POST['birthdate'];
		$contact = $_POST['contact'];
		$gender = $_POST['gender'];
		$position = $_POST['position'];
		$department = $_POST['department'];
		$schedule = $_POST['schedule'];

		// =========================================
		// PHOTO
		// =========================================

		$filename = $_FILES['photo']['name'];

		if(!empty($filename)){
			move_uploaded_file(
				$_FILES['photo']['tmp_name'],
				'../images/'.$filename
			);
		}

		// =========================================
		// GENERATE EMPLOYEE ID
		// =========================================

		$letters = '';
		$numbers = '';

		foreach(range('A', 'Z') as $char){
			$letters .= $char;
		}

		for($i = 0; $i < 10; $i++){
			$numbers .= $i;
		}

		$employee_id =
			substr(str_shuffle($letters), 0, 3).
			substr(str_shuffle($numbers), 0, 9);

		// =========================================
		// INSERT EMPLOYEE
		// =========================================

		$sql = "INSERT INTO employees (
					employee_id,
					firstname,
					lastname,
					address,
					birthdate,
					contact_info,
					gender,
					position_id,
					department,
					schedule_id,
					photo,
					created_on
				) VALUES (
					'$employee_id',
					'$firstname',
					'$lastname',
					'$address',
					'$birthdate',
					'$contact',
					'$gender',
					'$position',
					'$department',
					'$schedule',
					'$filename',
					NOW()
				)";

		if($conn->query($sql)){

			// =========================================
			// GET NEW EMPLOYEE ID
			// =========================================

			$new_employee_id = $conn->insert_id;

			// =========================================
			// SAVE SELECTED DEDUCTIONS
			// =========================================

			if(isset($_POST['deductions'])){

				foreach($_POST['deductions'] as $deduction_id){

					$sql2 = "INSERT INTO employee_deductions (
								employee_id,
								deduction_id,
								created_on
							) VALUES (
								'$new_employee_id',
								'$deduction_id',
								NOW()
							)";

					$conn->query($sql2);
				}
			}

			$_SESSION['success'] = 'Employee added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: employee.php');
?>
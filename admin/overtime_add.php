<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$employee = trim($_POST['employee']);
		$date     = $_POST['date'];
		$hours    = $_POST['hours'] + ($_POST['mins'] / 60);
		$rate     = $_POST['rate'];

		$lookup = $conn->prepare("SELECT id FROM employees WHERE employee_id = ?");
		$lookup->bind_param('s', $employee);
		$lookup->execute();
		$res = $lookup->get_result();

		if($res->num_rows < 1){
			$_SESSION['error'] = 'Employee not found';
		}else{
			$employee_id = (int) $res->fetch_assoc()['id'];

			$stmt = $conn->prepare("INSERT INTO overtime (employee_id, date_overtime, hours, rate) VALUES (?, ?, ?, ?)");
			$stmt->bind_param('isdd', $employee_id, $date, $hours, $rate);
			if($stmt->execute()){
				$_SESSION['success'] = 'Overtime added successfully';
			}else{
				$_SESSION['error'] = 'Operation failed. Please try again.';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: overtime.php');
	exit();
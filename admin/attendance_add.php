<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$employee = trim($_POST['employee']);
		$date     = $_POST['date'];
		$time_in  = date('H:i:s', strtotime($_POST['time_in']));
		$time_out = date('H:i:s', strtotime($_POST['time_out']));

		// Find employee by badge
		$lookup = $conn->prepare("SELECT id, schedule_id FROM employees WHERE employee_id = ?");
		$lookup->bind_param('s', $employee);
		$lookup->execute();
		$res = $lookup->get_result();

		if($res->num_rows < 1){
			$_SESSION['error'] = 'Employee not found';
		}
		else{
			$row = $res->fetch_assoc();
			$emp = (int) $row['id'];

			// Already has attendance for this date?
			$chk = $conn->prepare("SELECT id FROM attendance WHERE employee_id = ? AND date = ?");
			$chk->bind_param('is', $emp, $date);
			$chk->execute();

			if($chk->get_result()->num_rows > 0){
				$_SESSION['error'] = 'Employee attendance for the day exist';
			}
			else{
				// Schedule (for late status + hours clamp)
				$sched_id = (int) $row['schedule_id'];
				$sq = $conn->prepare("SELECT time_in, time_out FROM schedules WHERE id = ?");
				$sq->bind_param('i', $sched_id);
				$sq->execute();
				$scherow = $sq->get_result()->fetch_assoc();

				$sched_in  = $scherow ? $scherow['time_in']  : null;
				$sched_out = $scherow ? $scherow['time_out'] : null;

				$logstatus = ($sched_in && $time_in > $sched_in) ? 0 : 1;

				$ins = $conn->prepare("INSERT INTO attendance (employee_id, date, time_in, time_out, status) VALUES (?, ?, ?, ?, ?)");
				$ins->bind_param('isssi', $emp, $date, $time_in, $time_out, $logstatus);

				if($ins->execute()){
					$_SESSION['success'] = 'Attendance added successfully';
					$id = $conn->insert_id;

					// Clamp to schedule, then total worked hours (minus 1h break if span > 4h)
					$ci = $time_in;  $co = $time_out;
					if($sched_in  && $sched_in  > $ci){ $ci = $sched_in;  }
					if($sched_out && $sched_out < $co){ $co = $sched_out; }

					$hours = (strtotime($co) - strtotime($ci)) / 3600;
					if($hours > 4){ $hours -= 1; }
					if($hours < 0){ $hours = 0; }
					$hours = round($hours, 2);

					$upd = $conn->prepare("UPDATE attendance SET num_hr = ? WHERE id = ?");
					$upd->bind_param('di', $hours, $id);
					$upd->execute();
				}
				else{
					$_SESSION['error'] = 'Operation failed. Please try again.';
				}
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: attendance.php');
	exit();
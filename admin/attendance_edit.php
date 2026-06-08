<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id       = intval($_POST['id']);
		$date     = $_POST['edit_date'];
		$time_in  = date('H:i:s', strtotime($_POST['edit_time_in']));
		$time_out = date('H:i:s', strtotime($_POST['edit_time_out']));

		$upd = $conn->prepare("UPDATE attendance SET date = ?, time_in = ?, time_out = ? WHERE id = ?");
		$upd->bind_param('sssi', $date, $time_in, $time_out, $id);

		if($upd->execute()){
			$_SESSION['success'] = 'Attendance updated successfully';

			// Employee + schedule for this attendance row
			$aq = $conn->prepare("SELECT employee_id FROM attendance WHERE id = ?");
			$aq->bind_param('i', $id);
			$aq->execute();
			$arow = $aq->get_result()->fetch_assoc();
			$emp  = (int) $arow['employee_id'];

			$eq = $conn->prepare("SELECT schedules.time_in AS sched_in, schedules.time_out AS sched_out
			                      FROM employees
			                      LEFT JOIN schedules ON schedules.id = employees.schedule_id
			                      WHERE employees.id = ?");
			$eq->bind_param('i', $emp);
			$eq->execute();
			$srow = $eq->get_result()->fetch_assoc();

			$sched_in  = $srow ? $srow['sched_in']  : null;
			$sched_out = $srow ? $srow['sched_out'] : null;

			$logstatus = ($sched_in && $time_in > $sched_in) ? 0 : 1;

			// Clamp to schedule, then total worked hours (minus 1h break if span > 4h)
			$ci = $time_in;  $co = $time_out;
			if($sched_in  && $sched_in  > $ci){ $ci = $sched_in;  }
			if($sched_out && $sched_out < $co){ $co = $sched_out; }

			$hours = (strtotime($co) - strtotime($ci)) / 3600;
			if($hours > 4){ $hours -= 1; }
			if($hours < 0){ $hours = 0; }
			$hours = round($hours, 2);

			$nu = $conn->prepare("UPDATE attendance SET num_hr = ?, status = ? WHERE id = ?");
			$nu->bind_param('dii', $hours, $logstatus, $id);
			$nu->execute();
		}
		else{
			$_SESSION['error'] = 'Operation failed. Please try again.';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:attendance.php');
	exit();
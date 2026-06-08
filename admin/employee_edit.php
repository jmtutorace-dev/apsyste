<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

    $empid = intval($_POST['id']);

    if(empty($empid)){
        $_SESSION['error'] = "Employee ID missing";
        header('location: employee.php');
        exit();
    }

    $firstname    = trim($_POST['firstname']);
    $lastname     = trim($_POST['lastname']);
    $biometric_id = trim($_POST['biometric_id']);
    $address      = trim($_POST['address']);
    $birthdate    = $_POST['birthdate'];
    $contact      = trim($_POST['contact']);
    $gender       = $_POST['gender'];
    $position     = intval($_POST['position']);
    $schedule     = intval($_POST['schedule']);
    // UI sends department NAME (e.g., "Accounting"), but employees.department may be stored as an INT id.
    // To avoid MySQL coercing non-numeric strings into 0, map the name to department id.
    $department_name = trim($_POST['department']);
    $department = $department_name;

    $dept_lookup = $conn->prepare("SELECT id FROM departments WHERE name = ? LIMIT 1");
    if($dept_lookup){
        $dept_lookup->bind_param('s', $department_name);
        $dept_lookup->execute();
        $dept_lookup->bind_result($dept_id);
        if($dept_lookup->fetch()){
            $department = $dept_id;
        }
        $dept_lookup->close();
    }


    $stmt = $conn->prepare("UPDATE employees SET
        firstname=?, lastname=?, biometric_id=?, address=?, birthdate=?,
        contact_info=?, gender=?, position_id=?, schedule_id=?, department=?
        WHERE id=?");

    $stmt->bind_param(
        'sssssssiiii',
        $firstname, $lastname, $biometric_id, $address, $birthdate,
        $contact, $gender, $position, $schedule, $department, $empid
    );

    if($stmt->execute()){

        // DELETE OLD DEDUCTIONS
        $del = $conn->prepare("DELETE FROM employee_deductions WHERE employee_id=?");
        $del->bind_param('i', $empid);
        $del->execute();

        // INSERT NEW DEDUCTIONS
        if(isset($_POST['deductions'])){

            $ins = $conn->prepare("INSERT INTO employee_deductions (employee_id, deduction_id) VALUES (?, ?)");

            foreach($_POST['deductions'] as $deduction_id){
                $deduction_id = intval($deduction_id);
                $ins->bind_param('ii', $empid, $deduction_id);
                $ins->execute();
            }
        }

        $_SESSION['success'] = "Employee updated successfully";

    }
    else{
        $_SESSION['error'] = "Could not update employee. Please try again.";
    }

}
else{
    $_SESSION['error'] = "Edit button not triggered";
}

header('location: employee.php');
exit();
?>

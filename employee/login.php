<?php
    session_start();
    include __DIR__ . '/../conn.php';

    if(isset($_POST['login'])){

        $employee_id = trim($_POST['employee_id']);
        $password    = $_POST['password'];

        // Login with the Employee ID / badge number (unique)
        $stmt = $conn->prepare("SELECT id, password FROM employees WHERE employee_id = ? LIMIT 1");
        $stmt->bind_param('s', $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows < 1){
            $_SESSION['error'] = 'No employee account found for that Employee ID.';
        }
        else{
            $row = $result->fetch_assoc();

            if(!empty($row['password']) && password_verify($password, $row['password'])){
                session_regenerate_id(true);
                $_SESSION['employee'] = $row['id'];
                header('location: home.php');
                exit();
            }
            else{
                $_SESSION['error'] = 'Incorrect password.';
            }
        }
    }
    else{
        $_SESSION['error'] = 'Please enter your credentials.';
    }

    header('location: index.php');
    exit();
?>

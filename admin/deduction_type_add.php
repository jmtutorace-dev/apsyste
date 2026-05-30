<?php

include 'includes/session.php';

if(isset($_POST['add'])){

    $deduction_name = trim($_POST['deduction_name']);
    $description = trim($_POST['description']);

    if(empty($deduction_name)){

        $_SESSION['error'] = 'Deduction name is required';

    }
    else{

        $check = "
            SELECT *
            FROM deduction_types
            WHERE deduction_name = '$deduction_name'
        ";

        $query = $conn->query($check);

        if($query->num_rows > 0){

            $_SESSION['error'] =
                'Deduction type already exists';

        }
        else{

            $sql = "
                INSERT INTO deduction_types
                (
                    deduction_name,
                    description
                )
                VALUES
                (
                    '$deduction_name',
                    '$description'
                )
            ";

            if($conn->query($sql)){

                $_SESSION['success'] =
                    'Deduction type added successfully';

            }
            else{

                $_SESSION['error'] =
                    $conn->error;
            }
        }
    }
}
else{

    $_SESSION['error'] = 'Fill up add form first';
}

header('location: deduction_types.php');

?>
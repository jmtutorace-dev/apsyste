<?php

include 'includes/session.php';

if(isset($_POST['delete'])){

    $id = intval($_POST['id']);

    $sql = "DELETE FROM deduction_types WHERE id='$id'";

    if($conn->query($sql)){

        if($conn->affected_rows > 0){

            $_SESSION['success'] =
                'Deduction type deleted successfully';

        }
        else{

            $_SESSION['error'] =
                'No row was deleted. ID received: '.$id;
        }

    }
    else{

        $_SESSION['error'] =
            $conn->error;
    }

}
else{

    $_SESSION['error'] =
        'Delete button not detected';
}

header('location: deduction_types.php');
exit();

?>
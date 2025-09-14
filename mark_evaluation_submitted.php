<?php
include 'db_connect.php';
session_start();

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $update = $conn->query("UPDATE pending_evaluations SET status = 'submitted' WHERE id = $id");
    
    if($update){
        $_SESSION['success_msg'] = 'Evaluation marked as submitted successfully.';
    } else {
        $_SESSION['error_msg'] = 'Error marking evaluation as submitted: ' . $conn->error;
    }
}

header("Location: index.php?page=pending_evaluations");
exit();
?>
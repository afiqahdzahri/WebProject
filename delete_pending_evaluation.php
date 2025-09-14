<?php
include 'db_connect.php';
session_start();

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $delete = $conn->query("DELETE FROM pending_evaluations WHERE id = $id");
    
    if($delete){
        $_SESSION['success_msg'] = 'Pending evaluation deleted successfully.';
    } else {
        $_SESSION['error_msg'] = 'Error deleting pending evaluation: ' . $conn->error;
    }
}

header("Location: index.php?page=pending_evaluations");
exit();
?>
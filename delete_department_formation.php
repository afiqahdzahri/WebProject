<?php
include 'db_connect.php';

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $delete = $conn->query("DELETE FROM department_formation WHERE id = $id");
    
    if($delete) {
        header("Location: index.php?page=department_formation&msg=Department formation deleted successfully");
    } else {
        header("Location: index.php?page=department_formation&error=Error deleting department formation");
    }
} else {
    header("Location: index.php?page=department_formation&error=Invalid request");
}
exit();
?>
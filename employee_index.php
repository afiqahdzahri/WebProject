<?php
session_start();

if(!isset($_SESSION['login_id'])){
    header("Location: ../login.php");
    exit();
}
if($_SESSION['login_role'] !== 'employee'){
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
</head>
<body>
    <h1>Welcome, Employee <?php echo $_SESSION['login_email']; ?>!</h1>
    <p>You are logged in as <strong><?php echo $_SESSION['login_role']; ?></strong>.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>

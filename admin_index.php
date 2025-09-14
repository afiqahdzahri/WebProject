<?php
session_start();

// Protect page
if(!isset($_SESSION['login_id'])){
    header("Location: ../login.php");
    exit();
}
if($_SESSION['login_role'] !== 'admin'){
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin <?php echo $_SESSION['login_email']; ?>!</h1>
    <p>You are logged in as <strong><?php echo $_SESSION['login_role']; ?></strong>.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>

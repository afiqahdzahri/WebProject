<?php
// config.php - Database configuration file

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'epes_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Session settings (only if session not started yet)
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 0); // Session expires when browser closes
    ini_set('session.gc_maxlifetime', 1800); // 30 minutes
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('Asia/Kuala_Lumpur');

// Base URL (adjust according to your setup)
define('BASE_URL', 'http://localhost/epes/');

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['login_id']);
}

// Function to redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Function to check user role
function has_role($role) {
    return isset($_SESSION['login_role']) && $_SESSION['login_role'] == $role;
}

// Function to redirect based on role
function redirect_based_on_role() {
    if (is_logged_in()) {
        switch ($_SESSION['login_role']) {
            case 'admin':
                header("Location: home_admin.php");
                break;
            case 'evaluator':
                header("Location: home_evaluator.php");
                break;
            case 'employee':
                header("Location: home_employee.php");
                break;
            default:
                header("Location: login.php");
        }
        exit();
    }
}
?>

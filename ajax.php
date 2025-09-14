<?php
ob_start();
date_default_timezone_set("Asia/Manila");

// Check if action is provided via GET or POST
if(isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif(isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    // Handle error - no action specified
    echo "Error: No action specified";
    exit();
}

include 'admin_class.php';
$crud = new Action();

if($action == 'login'){
    $login = $crud->login();
    if($login){
        // Convert role names to numeric values expected by frontend
        if($login == 'admin'){
            echo json_encode(['status' => 'success', 'role' => 0, 'message' => 'Login successful']);
        } elseif($login == 'evaluator'){
            echo json_encode(['status' => 'success', 'role' => 1, 'message' => 'Login successful']);
        } elseif($login == 'employee'){
            echo json_encode(['status' => 'success', 'role' => 2, 'message' => 'Login successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid role']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username or password is incorrect']);
    }
    exit();
}

if($action == 'logout'){
    $logout = $crud->logout();
    if($logout)
        echo $logout;
}

// User CRUD
if($action == 'save_user'){
    $save = $crud->save_user();
    if($save) echo $save;
}

if($action == 'delete_user'){
    $save = $crud->delete_user();
    if($save) echo $save;
}

if($action == 'get_users'){
    $data = $crud->get_users();
    if($data) echo $data;
}

// Task CRUD
if($action == 'save_task'){
    $save = $crud->save_task();
    if($save) echo $save;
}

if($action == 'delete_task'){
    $save = $crud->delete_task();
    if($save) echo $save;
}

if($action == 'get_tasks'){
    $data = $crud->get_tasks();
    if($data) echo $data;
}

// Evaluation CRUD
if($action == 'save_evaluation'){
    $save = $crud->save_evaluation();
    if($save) echo $save;
}

if($action == 'get_evaluations'){
    $data = $crud->get_evaluations();
    if($data) echo $data;
}

// Department Formation CRUD
if($action == 'save_department_formation'){
    $save = $crud->save_department_formation();
    if($save) echo $save;
}

if($action == 'delete_department_formation'){
    $save = $crud->delete_department_formation();
    if($save) echo $save;
}

// Department List CRUD
if($action == 'save_department'){
    $save = $crud->save_department();
    if($save) echo $save;
}

if($action == 'delete_department'){
    $save = $crud->delete_department();
    if($save) echo $save;
}

// KPI Metrics CRUD - Use the CRUD class methods instead of direct database access
if($action == 'save_kpi'){
    $save = $crud->save_kpi();
    if($save) echo $save;
}

if($action == 'delete_kpi'){
    $save = $crud->delete_kpi();
    if($save) echo $save;
}

// Evaluation Reports CRUD
if($action == 'save_evaluation_report'){
    $save = $crud->save_evaluation_report();
    if($save) echo $save;
}

if($action == 'delete_evaluation_report'){
    $save = $crud->delete_evaluation_report();
    if($save) echo $save;
}

// Feedback History CRUD
if($action == 'save_feedback'){
    $save = $crud->save_feedback();
    if($save) echo $save;
}

if($action == 'delete_feedback'){
    $save = $crud->delete_feedback();
    if($save) echo $save;
}

// Improvement Objectives CRUD
if($action == 'save_improvement_objective'){
    $save = $crud->save_improvement_objective();
    if($save) echo $save;
}

if($action == 'delete_improvement_objective'){
    $save = $crud->delete_improvement_objective();
    if($save) echo $save;
}

// Performance Scores CRUD
if($action == 'save_performance_score'){
    $save = $crud->save_performance_score();
    if($save) echo $save;
}

if($action == 'delete_performance_score'){
    $save = $crud->delete_performance_score();
    if($save) echo $save;
}

// Attendance Records CRUD
if($action == 'save_attendance'){
    $save = $crud->save_attendance();
    if($save) echo $save;
}

if($action == 'delete_attendance'){
    $save = $crud->delete_attendance();
    if($save) echo $save;
}

// Ratings CRUD
if($action == 'save_rating'){
    $save = $crud->save_rating();
    if($save) echo $save;
}

if($action == 'delete_rating'){
    $save = $crud->delete_rating();
    if($save) echo $save;
}

// Pending Evaluations CRUD
if($action == 'save_pending_evaluation'){
    $save = $crud->save_pending_evaluation();
    if($save) echo $save;
}

if($action == 'delete_pending_evaluation'){
    $save = $crud->delete_pending_evaluation();
    if($save) echo $save;
}

// Mark evaluation as submitted
if($action == 'mark_evaluation_submitted'){
    $save = $crud->mark_evaluation_submitted();
    if($save) echo $save;
}

// Get department details for view modal
if($action == 'get_department_details'){
    $save = $crud->get_department_details();
    if($save) echo $save;
}

ob_end_flush();
?>
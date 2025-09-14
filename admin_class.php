<?php
// Check if session is not already active before starting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);

Class Action {
    private $db;
    private $conn;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        
        $this->db = $conn;
        $this->conn = $conn;
    }
    
    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

// Fixed login method
public function login() {
    extract($_POST);
    
    // Check if email and password are provided
    if(empty($email) || empty($password)) {
        return false;
    }
    
    $email = $this->db->real_escape_string($email);
    $login_type = intval($login);
    
    // Check users table first (for admin and evaluator)
    $qry = $this->db->query("SELECT * FROM users WHERE email = '$email'");
    
    if($qry->num_rows > 0) {
        $user = $qry->fetch_assoc();
        
        // Verify password - handle both hashed and plain text
        $password_valid = false;
        
        // First try password_verify for hashed passwords
        if(password_verify($password, $user['password'])) {
            $password_valid = true;
        }
        // Fallback: check if password matches directly (for plain text passwords)
        elseif($user['password'] === $password) {
            $password_valid = true;
            // Upgrade to hashed password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $this->db->query("UPDATE users SET password = '$hashed_password' WHERE id = {$user['id']}");
        }
        
        if($password_valid) {
            // Check if user role matches the selected login type
            $role_match = false;
            $user_role = $user['role'];
            
            switch($login_type) {
                case 0: // Admin
                    $role_match = ($user_role == 'admin');
                    break;
                case 1: // Evaluator
                    $role_match = ($user_role == 'evaluator');
                    break;
                case 2: // Employee
                    // Employees should not login through users table
                    $role_match = false;
                    break;
            }
            
            if($role_match) {
                $_SESSION['login_id'] = $user['id'];
                $_SESSION['login_type'] = $login_type;
                $_SESSION['login_name'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['login_avatar'] = $user['avatar'];
                $_SESSION['login_firstname'] = $user['firstname'];
                return $user_role; // Return the actual role string
            }
        }
    }
    
    // If not found in users table, check employee table for employee login
    if($login_type == 2) {
        $emp_qry = $this->db->query("SELECT * FROM employee_list WHERE email = '$email'");
        
        if($emp_qry->num_rows > 0) {
            $employee = $emp_qry->fetch_assoc();
            
            // Verify password for employee - handle both hashed and plain text
            $password_valid = false;
            
            if(password_verify($password, $employee['password'])) {
                $password_valid = true;
            }
            // Fallback: check if password matches directly
            elseif($employee['password'] === $password) {
                $password_valid = true;
                // Upgrade to hashed password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $this->db->query("UPDATE employee_list SET password = '$hashed_password' WHERE id = {$employee['id']}");
            }
            
            if($password_valid) {
                $_SESSION['login_id'] = $employee['id'];
                $_SESSION['login_type'] = 2;
                $_SESSION['login_name'] = $employee['firstname'] . ' ' . $employee['lastname'];
                $_SESSION['login_avatar'] = $employee['avatar'];
                $_SESSION['login_firstname'] = $employee['firstname'];
                return 'employee'; // Return employee role
            }
        }
    }
    
    return false;
}

    public function logout() {
        session_destroy();
        // Return a JSON response instead of just 1
        return json_encode(['status' => 'success', 'redirect' => 'login.php']);
    }
    
    // Save user method
    public function save_user() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO users SET $data");
        } else {
            $save = $this->db->query("UPDATE users SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete user method
    public function delete_user() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM users WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Get users method
    public function get_users() {
        $qry = $this->db->query("SELECT * FROM users ORDER BY firstname, lastname");
        $data = array();
        while($row = $qry->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode($data);
    }
    
    // Save task method
    public function save_task() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO task_list SET $data");
        } else {
            $save = $this->db->query("UPDATE task_list SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete task method
    public function delete_task() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM task_list WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Get tasks method
    public function get_tasks() {
        $qry = $this->db->query("SELECT * FROM task_list ORDER BY due_date");
        $data = array();
        while($row = $qry->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode($data);
    }
    
    // Save evaluation method
    public function save_evaluation() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO ratings SET $data");
        } else {
            $save = $this->db->query("UPDATE ratings SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Get evaluations method
    public function get_evaluations() {
        $qry = $this->db->query("SELECT * FROM ratings ORDER BY date_created DESC");
        $data = array();
        while($row = $qry->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode($data);
    }
    
    // Save department formation method
    public function save_department_formation() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id')) && !empty($v)){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = " . ($v === '' ? "NULL" : "'$v'");
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO department_formation SET $data");
        } else {
            $save = $this->db->query("UPDATE department_formation SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return $this->db->error;
    }
    
    // Delete department formation method
    public function delete_department_formation() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM department_formation WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save department method
    public function save_department() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO department_list SET $data");
        } else {
            $save = $this->db->query("UPDATE department_list SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete department method
    public function delete_department() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM department_list WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save KPI method
    public function save_kpi() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO kpi_metrics SET $data");
        } else {
            $save = $this->db->query("UPDATE kpi_metrics SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete KPI method
    public function delete_kpi() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM kpi_metrics WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save evaluation report method
    public function save_evaluation_report() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO evaluation_reports SET $data");
        } else {
            $save = $this->db->query("UPDATE evaluation_reports SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete evaluation report method
    public function delete_evaluation_report() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM evaluation_reports WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save feedback method
    public function save_feedback() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO feedback_history SET $data");
        } else {
            $save = $this->db->query("UPDATE feedback_history SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete feedback method
    public function delete_feedback() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM feedback_history WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save improvement objective method
    public function save_improvement_objective() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO improvement_objectives SET $data");
        } else {
            $save = $this->db->query("UPDATE improvement_objectives SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete improvement objective method
    public function delete_improvement_objective() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM improvement_objectives WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save performance score method
    public function save_performance_score() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO performance_scores SET $data");
        } else {
            $save = $this->db->query("UPDATE performance_scores SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete performance score method
    public function delete_performance_score() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM performance_scores WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save attendance method
    public function save_attendance() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO attendance_records SET $data");
        } else {
            $save = $this->db->query("UPDATE attendance_records SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete attendance method
    public function delete_attendance() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM attendance_records WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save rating method
    public function save_rating() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO ratings SET $data");
        } else {
            $save = $this->db->query("UPDATE ratings SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete rating method
    public function delete_rating() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM ratings WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Save pending evaluation method
    public function save_pending_evaluation() {
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= "`$k` = '$v'";
            }
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO pending_evaluations SET $data");
        } else {
            $save = $this->db->query("UPDATE pending_evaluations SET $data WHERE id = $id");
        }
        if($save){
            return 1;
        }
        return 0;
    }
    
    // Delete pending evaluation method
    public function delete_pending_evaluation() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM pending_evaluations WHERE id = $id");
        if($delete){
            return 1;
        }
        return 0;
    }
    
    // Mark evaluation as submitted method
    public function mark_evaluation_submitted() {
        extract($_POST);
        $update = $this->db->query("UPDATE pending_evaluations SET status = 'submitted' WHERE id = $id");
        if($update){
            return 1;
        }
        return 0;
    }
    
    // Get department details method
    public function get_department_details() {
        $id = intval($_POST['id']);
        
        $qry = $this->db->query("SELECT df.*, 
                        m.firstname as m_firstname, m.lastname as m_lastname,
                        tl.firstname as tl_firstname, tl.lastname as tl_lastname
                        FROM department_formation df
                        LEFT JOIN users m ON df.manager_id = m.id
                        LEFT JOIN users tl ON df.team_lead_id = tl.id
                        WHERE df.id = '$id'");
        
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            $details = '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Department Name:</strong> '.htmlspecialchars($row['department_name']).'</p>
                        <p><strong>Manager:</strong> '.htmlspecialchars($row['m_firstname'].' '.$row['m_lastname']).'</p>
                        <p><strong>Team Lead:</strong> '.($row['team_lead_id'] ? htmlspecialchars($row['tl_firstname'].' '.$row['tl_lastname']) : 'Not Assigned').'</p>
                        <p><strong>Description:</strong> '.($row['description'] ? htmlspecialchars($row['description']) : 'No description provided').'</p>
                        <p><strong>Date Created:</strong> '.date("F d, Y", strtotime($row['date_created'])).'</p>
                    </div>
                </div>
            </div>';
            return $details;
        } else {
            return "Department details not found.";
        }
    }
}
?>

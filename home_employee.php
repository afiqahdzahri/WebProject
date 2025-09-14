<div class="container-fluid">
    <div class="row">
        <?php
        $emp_id = $_SESSION['login_id'];
        
        // Get employee details
        $emp_qry = $conn->query("SELECT * FROM employee_list WHERE id = '$emp_id'");
        $employee = $emp_qry->fetch_assoc();
        $employee_email = $employee['email'];
        
        // Get user ID from users table
        $user_qry = $conn->query("SELECT id FROM users WHERE email = '$employee_email'");
        $user = $user_qry->fetch_assoc();
        $user_id = $user['id'];
        
        // Get task counts
        $my_tasks = $conn->query("SELECT id FROM task_list WHERE employee_id='$emp_id'")->num_rows;
        $completed = $conn->query("SELECT id FROM task_list WHERE employee_id='$emp_id' AND status=2")->num_rows;
        $pending = $my_tasks - $completed;
        
        // Get attendance records count
        $attendance_count = $conn->query("SELECT id FROM attendance_records WHERE employee_id='$emp_id'")->num_rows;
        
        // Get feedback history count
        $feedback_count = $conn->query("SELECT id FROM feedback_history WHERE employee_id='$emp_id'")->num_rows;
        
        // Get improvement objectives count
        $objectives_count = $conn->query("SELECT id FROM improvement_objectives WHERE employee_id='$emp_id'")->num_rows;
        
        // Get ratings count
        $ratings_count = $conn->query("SELECT id FROM ratings WHERE employee_id='$emp_id'")->num_rows;
        
        // Get current month attendance percentage
        $current_month = date('Y-m');
        $present_days = $conn->query("SELECT COUNT(*) as present FROM attendance_records WHERE employee_id='$emp_id' AND status='present' AND DATE_FORMAT(attendance_date, '%Y-%m') = '$current_month'")->fetch_assoc()['present'];
        $total_working_days = $conn->query("SELECT COUNT(DISTINCT attendance_date) as total FROM attendance_records WHERE employee_id='$emp_id' AND DATE_FORMAT(attendance_date, '%Y-%m') = '$current_month'")->fetch_assoc()['total'];
        $attendance_percentage = $total_working_days > 0 ? round(($present_days / $total_working_days) * 100, 2) : 0;

        // AI Insights
        require_once 'ai_functions.php';
        $ai_insights = getAIAnalysis($emp_id);
        ?>

        <!-- Performance Ratings -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?php echo $ratings_count ?></h3>
                    <p>Performance Reviews</p>
                </div>
                <div class="icon">
                    <i class="fas fa-star"></i>
                </div>
                <a href="index.php?page=performance_scores" class="small-box-footer">View Ratings <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Improvement Objectives -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?php echo $objectives_count ?></h3>
                    <p>Improvement Goals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <a href="index.php?page=improvement_objectives" class="small-box-footer">View Objectives <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Feedback History -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $feedback_count ?></h3>
                    <p>Feedback Received</p>
                </div>
                <div class="icon">
                    <i class="fas fa-comments"></i>
                </div>
                <a href="index.php?page=feedback_history" class="small-box-footer">View Feedback <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo round($ai_insights['prediction'], 2) ?>%</h3>
                    <p>AI Performance Prediction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-robot"></i>
                </div>
                <a href="index.php?page=ai_insights" class="small-box-footer">View AI Insights <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
    color: white;
}
.bg-purple:hover {
    background-color: #5a2d91 !important;
}
.small-box .icon {
    transition: all 0.3s ease;
}
.small-box:hover .icon {
    transform: scale(1.1);
}
</style>

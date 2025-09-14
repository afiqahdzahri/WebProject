<div class="container-fluid">
    <div class="row">
        <?php
        $eval_id = $_SESSION['login_id'];

        // Get evaluator details
        $eval_qry = $conn->query("SELECT * FROM evaluator_list WHERE user_id = '$eval_id'");
        $evaluator = $eval_qry->fetch_assoc();
        
        // Pending evaluations assigned to this evaluator
        $pending_evaluations = $conn->query("SELECT id FROM pending_evaluations WHERE evaluator_id='$eval_id' AND status='pending'")->num_rows;
        
        // Total employees assigned to this evaluator
        $total_employees = $conn->query("SELECT id FROM employee_list WHERE evaluator_id='$eval_id'")->num_rows;
        
        // Total evaluations (all statuses)
        $total_evaluations = $conn->query("SELECT id FROM pending_evaluations WHERE evaluator_id='$eval_id'")->num_rows;
        
        // Attendance percentage (for evaluator’s team)
        $total_attendance = $conn->query("SELECT COUNT(*) as total FROM attendance_records ar 
                                         JOIN employee_list e ON ar.employee_id = e.id 
                                         WHERE e.evaluator_id = '$eval_id'")->fetch_assoc()['total'];
        
        $present_count = $conn->query("SELECT COUNT(*) as present FROM attendance_records ar 
                                      JOIN employee_list e ON ar.employee_id = e.id 
                                      WHERE e.evaluator_id = '$eval_id' AND ar.status = 'present'")->fetch_assoc()['present'];
        
        $attendance_percentage = $total_attendance > 0 ? round(($present_count / $total_attendance) * 100, 1) : 0;

        // AI Insights for evaluator’s employees (average of predictions)
        require_once 'ai_functions.php';
        $employee_qry = $conn->query("SELECT id FROM employee_list WHERE evaluator_id='$eval_id'");
        
        $predictions = [];
        $sentiments = [];
        $last_analyzed = null;
        
        while ($emp = $employee_qry->fetch_assoc()) {
            $ai_data = getAIAnalysis($emp['id']);
            $predictions[] = $ai_data['prediction'];
            $sentiments[] = $ai_data['sentiment_score'] * 100;
            $last_analyzed = $ai_data['last_analyzed']; // latest overwrite
        }
        
        $avg_prediction = count($predictions) > 0 ? round(array_sum($predictions) / count($predictions), 2) : 0;
        $avg_sentiment = count($sentiments) > 0 ? round(array_sum($sentiments) / count($sentiments), 1) : 0;
        ?>
        
        <!-- Pending Evaluations -->
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $pending_evaluations ?></h3>
                    <p>Pending Evaluations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-edit"></i>
                </div>
                <a href="index.php?page=pending_evaluations" class="small-box-footer">Evaluate Now <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Assigned Employees -->
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $total_employees ?></h3>
                    <p>Assigned Employees</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="index.php?page=employee_list" class="small-box-footer">View Employees <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <!-- Attendance Records -->
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo $attendance_percentage ?>%</h3>
                    <p>Attendance Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="index.php?page=attendance_records" class="small-box-footer">View Attendance <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- AI Insights (Evaluator View) -->
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-primary" style="cursor: pointer;" onclick="location.href='index.php?page=ai_insights'">
                <div class="inner">
                    <h3><?php echo $avg_prediction ?>%</h3>
                    <p>Team AI Prediction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="small-box-footer">View AI Insights <i class="fas fa-arrow-circle-right"></i></div>
            </div>
        </div>

    </div>
</div>

<style>
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,0.125), 0 1px 3px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
    min-height: 150px;
    position: relative;
    background: #fff;
}
.small-box > .inner {
    padding: 10px;
    color: #fff;
}
.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    z-index: 10;
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
}
.small-box h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}
.small-box p {
    font-size: 1rem;
}
.small-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}
.small-box:hover .icon {
    transform: scale(1.1);
}
.bg-success { background-color: #28a745 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-primary { background-color: #007bff !important; }
.mb-3 { margin-bottom: 1rem !important; }
</style>

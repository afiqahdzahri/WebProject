<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    echo "<script>alert('Please login first'); window.location.href = 'login.php';</script>";
    exit;
}

include 'db_connect.php';

// Check if ID parameter is provided
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<script>alert('Performance Score ID not provided'); window.location.href = 'performance_scores.php';</script>";
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Fetch score data
$score_query = $conn->query("SELECT * FROM performance_scores WHERE id = '$id'");
if($score_query->num_rows == 0){
    echo "<script>alert('Performance score not found'); window.location.href = 'performance_scores.php';</script>";
    exit;
}

$score = $score_query->fetch_assoc();

// Fetch employee name
$employee_query = $conn->query("SELECT CONCAT(firstname, ' ', lastname) as name FROM employee_list WHERE id = '".$score['employee_id']."'");
$employee = $employee_query->fetch_assoc();

// Fetch evaluator name
$evaluator_query = $conn->query("SELECT CONCAT(firstname, ' ', lastname) as name FROM users WHERE id = '".$score['evaluator_id']."'");
$evaluator = $evaluator_query->fetch_assoc();

// Determine score color
$overall_score = $score['overall_score'];
$score_color = '';
if($overall_score >= 90) {
    $score_color = 'success';
} elseif($overall_score >= 80) {
    $score_color = 'primary';
} elseif($overall_score >= 70) {
    $score_color = 'warning';
} else {
    $score_color = 'danger';
}
?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h5 class="card-title"><b><i class="fa fa-chart-line"></i> Performance Score Details</b></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee">Employee</label>
                            <p class="form-control-static"><b><?php echo ucwords($employee['name']); ?></b></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="evaluation_period">Evaluation Period</label>
                            <p class="form-control-static"><b><?php echo $score['evaluation_period']; ?></b></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="productivity_score">Productivity Score</label>
                            <p class="form-control-static"><b><?php echo $score['productivity_score']; ?></b></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="attendance_score">Attendance Score</label>
                            <p class="form-control-static"><b><?php echo $score['attendance_score']; ?></b></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="utilization_score">Utilization Score</label>
                            <p class="form-control-static"><b><?php echo $score['utilization_score']; ?></b></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="quality_score">Quality Score</label>
                            <p class="form-control-static"><b><?php echo $score['quality_score']; ?></b></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="overall_score">Overall Score</label>
                            <p class="form-control-static">
                                <span class="badge badge-<?php echo $score_color; ?>"><?php echo $score['overall_score']; ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="evaluator">Evaluator</label>
                            <p class="form-control-static"><b><?php echo ucwords($evaluator['name']); ?></b></p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="date_created">Date Created</label>
                    <p class="form-control-static"><b><?php echo date("M d, Y h:i A", strtotime($score['date_created'])); ?></b></p>
                </div>
                
                <div class="form-group text-center">
                    <a href="performance_scores.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to List</a>
                    <a href="edit_performance_score.php?id=<?php echo $score['id']; ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    header("location: login.php");
    exit;
}

include 'db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $evaluation_period = $_POST['evaluation_period'];
    $productivity_score = $_POST['productivity_score'];
    $attendance_score = $_POST['attendance_score'];
    $utilization_score = $_POST['utilization_score'];
    $quality_score = $_POST['quality_score'];
    $evaluator_id = $_SESSION['login_id']; // Assuming the logged-in user is the evaluator
    
    // Calculate overall score (simple average)
    $overall_score = ($productivity_score + $attendance_score + $utilization_score + $quality_score) / 4;
    
    $insert_query = $conn->query("INSERT INTO performance_scores 
                                (employee_id, evaluation_period, productivity_score, attendance_score, utilization_score, quality_score, overall_score, evaluator_id) 
                                VALUES ('$employee_id', '$evaluation_period', '$productivity_score', '$attendance_score', '$utilization_score', '$quality_score', '$overall_score', '$evaluator_id')");
    
    if ($insert_query) {
        $_SESSION['success_msg'] = "Performance score successfully added";
        header("Location: performance_scores.php");
        exit;
    } else {
        $error_msg = "Error adding performance score: " . $conn->error;
    }
}

// Fetch employees for dropdown
$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY firstname, lastname");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Performance Score</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Add New Performance Score</h3>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="performance_scores.php"><i class="fa fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select class="form-control" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php while($employee = $employees->fetch_assoc()): ?>
                                <option value="<?php echo $employee['id']; ?>"><?php echo $employee['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="evaluation_period">Evaluation Period</label>
                        <input type="text" class="form-control" id="evaluation_period" name="evaluation_period" placeholder="e.g., Q1 2023, January 2023" required>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="productivity_score">Productivity Score (0-100)</label>
                            <input type="number" min="0" max="100" class="form-control" id="productivity_score" name="productivity_score" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="attendance_score">Attendance Score (0-100)</label>
                            <input type="number" min="0" max="100" class="form-control" id="attendance_score" name="attendance_score" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="utilization_score">Utilization Score (0-100)</label>
                            <input type="number" min="0" max="100" class="form-control" id="utilization_score" name="utilization_score" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="quality_score">Quality Score (0-100)</label>
                            <input type="number" min="0" max="100" class="form-control" id="quality_score" name="quality_score" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Performance Score</button>
                    <a href="performance_scores.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
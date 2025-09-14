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
    $kpi_metric_id = $_POST['kpi_metric_id'];
    $objective_text = $_POST['objective_text'];
    $current_value = $_POST['current_value'];
    $target_value = $_POST['target_value'];
    $target_date = $_POST['target_date'];
    $status = $_POST['status'];
    
    $insert_query = $conn->query("INSERT INTO improvement_objectives 
                                (employee_id, kpi_metric_id, objective_text, current_value, target_value, target_date, status) 
                                VALUES ('$employee_id', '$kpi_metric_id', '$objective_text', '$current_value', '$target_value', '$target_date', '$status')");
    
    if ($insert_query) {
        $_SESSION['success_msg'] = "Objective successfully added";
        header("Location: improvement_objectives.php");
        exit;
    } else {
        $error_msg = "Error adding objective: " . $conn->error;
    }
}

// Fetch employees and KPI metrics for dropdowns
$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY firstname, lastname");
$kpi_metrics = $conn->query("SELECT id, metric_name FROM kpi_metrics ORDER BY metric_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Improvement Objective</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Add New Improvement Objective</h3>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="improvement_objectives.php"><i class="fa fa-arrow-left"></i> Back to List</a>
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
                        <label for="kpi_metric_id">KPI Metric</label>
                        <select class="form-control" id="kpi_metric_id" name="kpi_metric_id" required>
                            <option value="">Select KPI Metric</option>
                            <?php while($metric = $kpi_metrics->fetch_assoc()): ?>
                                <option value="<?php echo $metric['id']; ?>"><?php echo $metric['metric_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="objective_text">Objective</label>
                        <textarea class="form-control" id="objective_text" name="objective_text" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_value">Current Value</label>
                        <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_value">Target Value</label>
                        <input type="number" step="0.01" class="form-control" id="target_value" name="target_value" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_date">Target Date</label>
                        <input type="date" class="form-control" id="target_date" name="target_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Objective</button>
                    <a href="improvement_objectives.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
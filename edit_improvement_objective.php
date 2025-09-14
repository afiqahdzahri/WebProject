<?php
// Start output buffering to prevent headers already sent error
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

// Check if ID parameter is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: improvement_objectives.php");
    exit;
}

$id = $_GET['id'];

// Fetch objective details
$query = $conn->query("SELECT * FROM improvement_objectives WHERE id = $id");
if (!$query || $query->num_rows === 0) {
    header("Location: improvement_objectives.php");
    exit;
}

$objective = $query->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $kpi_metric_id = isset($_POST['kpi_metric_id']) ? $_POST['kpi_metric_id'] : null;
    $objective_text = $_POST['objective_text'];
    $current_value = $_POST['current_value'];
    $target_value = $_POST['target_value'];
    $target_date = $_POST['target_date'];
    $status = $_POST['status'];
    
    // Check if kpi_metric_id column exists in the table
    $checkColumn = $conn->query("SHOW COLUMNS FROM improvement_objectives LIKE 'kpi_metric_id'");
    if ($checkColumn->num_rows > 0) {
        // Column exists, include it in the update
        $update_query = $conn->query("UPDATE improvement_objectives 
                                    SET employee_id = '$employee_id', 
                                        kpi_metric_id = " . ($kpi_metric_id ? "'$kpi_metric_id'" : "NULL") . ", 
                                        objective_text = '$objective_text', 
                                        current_value = '$current_value', 
                                        target_value = '$target_value', 
                                        target_date = '$target_date', 
                                        status = '$status'
                                    WHERE id = $id");
    } else {
        // Column doesn't exist, exclude it from the update
        $update_query = $conn->query("UPDATE improvement_objectives 
                                    SET employee_id = '$employee_id', 
                                        objective_text = '$objective_text', 
                                        current_value = '$current_value', 
                                        target_value = '$target_value', 
                                        target_date = '$target_date', 
                                        status = '$status'
                                    WHERE id = $id");
    }
    
    if ($update_query) {
        $_SESSION['success_msg'] = "Objective successfully updated";
        header("Location: improvement_objectives.php");
        exit;
    } else {
        $error_msg = "Error updating objective: " . $conn->error;
    }
}

// Fetch employees and KPI metrics for dropdowns
$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY firstname, lastname");
$kpi_metrics = $conn->query("SELECT id, metric_name FROM kpi_metrics ORDER BY metric_name");

// Check if kpi_metric_id column exists and get its value safely
$current_kpi_metric_id = null;
$checkColumn = $conn->query("SHOW COLUMNS FROM improvement_objectives LIKE 'kpi_metric_id'");
if ($checkColumn->num_rows > 0 && isset($objective['kpi_metric_id'])) {
    $current_kpi_metric_id = $objective['kpi_metric_id'];
}

// End output buffering and flush
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Improvement Objective</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Edit Improvement Objective</h3>
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
                            <?php 
                            $employees->data_seek(0); // Reset pointer
                            while($employee = $employees->fetch_assoc()): ?>
                                <option value="<?php echo $employee['id']; ?>" <?php echo $employee['id'] == $objective['employee_id'] ? 'selected' : ''; ?>>
                                    <?php echo $employee['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <?php
                    // Check if kpi_metric_id column exists before showing the dropdown
                    $checkColumn = $conn->query("SHOW COLUMNS FROM improvement_objectives LIKE 'kpi_metric_id'");
                    if ($checkColumn->num_rows > 0): ?>
                    <div class="form-group">
                        <label for="kpi_metric_id">KPI Metric</label>
                        <select class="form-control" id="kpi_metric_id" name="kpi_metric_id">
                            <option value="">Select KPI Metric (Optional)</option>
                            <?php 
                            $kpi_metrics->data_seek(0); // Reset pointer
                            while($metric = $kpi_metrics->fetch_assoc()): ?>
                                <option value="<?php echo $metric['id']; ?>" <?php echo ($current_kpi_metric_id !== null && $metric['id'] == $current_kpi_metric_id) ? 'selected' : ''; ?>>
                                    <?php echo $metric['metric_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="objective_text">Objective</label>
                        <textarea class="form-control" id="objective_text" name="objective_text" rows="3" required><?php echo htmlspecialchars($objective['objective_text']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_value">Current Value</label>
                        <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" value="<?php echo htmlspecialchars($objective['current_value']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_value">Target Value</label>
                        <input type="number" step="0.01" class="form-control" id="target_value" name="target_value" value="<?php echo htmlspecialchars($objective['target_value']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_date">Target Date</label>
                        <input type="date" class="form-control" id="target_date" name="target_date" value="<?php echo htmlspecialchars($objective['target_date']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="ongoing" <?php echo $objective['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="completed" <?php echo $objective['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="overdue" <?php echo $objective['status'] == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Objective</button>
                    <a href="improvement_objectives.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
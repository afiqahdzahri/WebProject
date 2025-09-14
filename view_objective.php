<?php
// Check if session is not already active before starting
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
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: improvement_objectives.php");
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Fetch objective data
$objective_query = $conn->query("SELECT * FROM improvement_objectives WHERE id = '$id'");
if($objective_query->num_rows == 0){
    header("Location: improvement_objectives.php");
    exit;
}

$objective = $objective_query->fetch_assoc();

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $employee_id = $conn->real_escape_string($_POST['employee_id']);
    $objective_text = $conn->real_escape_string($_POST['objective_text']);
    $kpi_metric_id = $conn->real_escape_string($_POST['kpi_metric_id']);
    $current_value = $conn->real_escape_string($_POST['current_value']);
    $target_value = $conn->real_escape_string($_POST['target_value']);
    $target_date = $conn->real_escape_string($_POST['target_date']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Update database
    $update = $conn->query("UPDATE improvement_objectives 
                           SET employee_id = '$employee_id', 
                               objective_text = '$objective_text',
                               kpi_metric_id = '$kpi_metric_id',
                               current_value = '$current_value',
                               target_value = '$target_value',
                               target_date = '$target_date',
                               status = '$status'
                           WHERE id = '$id'");
    
    if($update){
        $_SESSION['success_msg'] = "Improvement objective updated successfully!";
        header("Location: improvement_objectives.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch employees and KPI metrics for dropdowns
$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY name");
$kpi_metrics = $conn->query("SELECT id, metric_name FROM kpi_metrics ORDER BY metric_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Improvement Objective</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">View/Edit Improvement Objective</h3>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="improvement_objectives.php"><i class="fa fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select class="form-control" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php while($row = $employees->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $objective['employee_id'] == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo ucwords($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="objective_text">Objective</label>
                        <textarea class="form-control" id="objective_text" name="objective_text" rows="3" required><?php echo htmlspecialchars($objective['objective_text']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="kpi_metric_id">KPI Metric</label>
                        <select class="form-control" id="kpi_metric_id" name="kpi_metric_id" required>
                            <option value="">Select KPI Metric</option>
                            <?php while($row = $kpi_metrics->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $objective['kpi_metric_id'] == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo $row['metric_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="current_value">Current Value</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" value="<?php echo $objective['current_value']; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="target_value">Target Value</label>
                            <input type="number" step="0.01" class="form-control" id="target_value" name="target_value" value="<?php echo $objective['target_value']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_date">Target Date</label>
                        <input type="date" class="form-control" id="target_date" name="target_date" value="<?php echo $objective['target_date']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="ongoing" <?php echo $objective['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="completed" <?php echo $objective['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="overdue" <?php echo $objective['status'] == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        </select>
                    </div>
                    
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                        <a href="improvement_objectives.php" class="btn btn-secondary"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
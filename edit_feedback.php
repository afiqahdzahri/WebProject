<?php
// Check if session is not already active before starting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Check if ID parameter is provided
if(!isset($_GET['id']) || empty($_GET['id'])){
    die("Feedback ID not provided.");
}

$id = $conn->real_escape_string($_GET['id']);

// Fetch feedback data
$feedback_query = $conn->query("SELECT fh.*, 
                                emp.firstname as emp_firstname, emp.lastname as emp_lastname,
                                eval.firstname as eval_firstname, eval.lastname as eval_lastname
                                FROM feedback_history fh
                                LEFT JOIN employee_list emp ON fh.employee_id = emp.id
                                LEFT JOIN users eval ON fh.evaluator_id = eval.id
                                WHERE fh.id = '$id'");
                                
if($feedback_query->num_rows == 0){
    die("Feedback not found.");
}

$feedback = $feedback_query->fetch_assoc();

// Handle form submission - MUST be before any HTML output
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_text = $conn->real_escape_string($_POST['feedback_text']);
    $feedback_type = $conn->real_escape_string($_POST['feedback_type']);
    
    // Check if date_updated column exists in the table
    $check_column = $conn->query("SHOW COLUMNS FROM feedback_history LIKE 'date_updated'");
    
    if($check_column->num_rows > 0) {
        // Column exists, include it in the update
        $update_query = $conn->query("UPDATE feedback_history 
                                     SET feedback_text = '$feedback_text', 
                                         feedback_type = '$feedback_type',
                                         date_updated = NOW()
                                     WHERE id = '$id'");
    } else {
        // Column doesn't exist, update without it
        $update_query = $conn->query("UPDATE feedback_history 
                                     SET feedback_text = '$feedback_text', 
                                         feedback_type = '$feedback_type'
                                     WHERE id = '$id'");
    }
    
    if($update_query) {
        $_SESSION['success_msg'] = "Feedback updated successfully!";
        // Use JavaScript redirect instead of header() to avoid issues
        // Check if we're in a subdirectory and adjust the path accordingly
        echo '<script>
            // Get the current path and navigate to the correct page
            var currentPath = window.location.pathname;
            var basePath = currentPath.substring(0, currentPath.lastIndexOf("/"));
            
            // Try different possible locations for the feedback list
            if (window.location.href.indexOf("index.php") > -1) {
                window.location.href = "index.php?page=feedback_history";
            } else {
                // Try the most common locations
                window.location.href = "feedback_history.php";
            }
        </script>';
        exit();
    } else {
        $error_msg = "Error updating feedback: " . $conn->error;
    }
}

// Determine the correct cancel URL
$cancel_url = "feedback_history.php";
if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index.php') !== false) {
    $cancel_url = "index.php?page=feedback_history";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Feedback</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_msg'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Employee</th>
                                <td><?php echo ucwords($feedback['emp_firstname'] . ' ' . $feedback['emp_lastname']); ?></td>
                            </tr>
                            <tr>
                                <th>Evaluator</th>
                                <td><?php echo ucwords($feedback['eval_firstname'] . ' ' . $feedback['eval_lastname']); ?></td>
                            </tr>
                            <tr>
                                <th>Original Feedback Date</th>
                                <td><?php echo date("M d, Y H:i", strtotime($feedback['feedback_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Feedback Type</th>
                                <td>
                                    <select name="feedback_type" class="form-control" required>
                                        <option value="positive" <?php echo $feedback['feedback_type'] == 'positive' ? 'selected' : ''; ?>>Positive</option>
                                        <option value="constructive" <?php echo $feedback['feedback_type'] == 'constructive' ? 'selected' : ''; ?>>Constructive</option>
                                        <option value="coaching" <?php echo $feedback['feedback_type'] == 'coaching' ? 'selected' : ''; ?>>Coaching</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Feedback Text</th>
                                <td>
                                    <textarea name="feedback_text" class="form-control" rows="5" required><?php echo htmlspecialchars($feedback['feedback_text']); ?></textarea>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="<?php echo $cancel_url; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Form validation
        $('form').submit(function(e){
            var valid = true;
            $(this).find('select[required], textarea[required]').each(function(){
                if($(this).val() === ''){
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if(!valid){
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>

</body>
</html>
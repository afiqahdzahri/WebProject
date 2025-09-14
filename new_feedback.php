<?php
// Check if session is not already active before starting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if(!isset($_SESSION['login_id'])){
    header('location:login.php');
    exit;
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $employee_id = $conn->real_escape_string($_POST['employee_id']);
    $evaluator_id = $conn->real_escape_string($_POST['evaluator_id']);
    $feedback_type = $conn->real_escape_string($_POST['feedback_type']);
    $feedback_text = $conn->real_escape_string($_POST['feedback_text']);
    $feedback_date = date('Y-m-d H:i:s');
    
    // Insert into database
    $insert = $conn->query("INSERT INTO feedback_history (employee_id, evaluator_id, feedback_type, feedback_text, feedback_date) 
                           VALUES ('$employee_id', '$evaluator_id', '$feedback_type', '$feedback_text', '$feedback_date')");
    
    if($insert){
        // Redirect to feedback history with success message
        $_SESSION['success_msg'] = "Feedback added successfully!";
        header("Location: index.php?page=feedback_history");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch employees and evaluators for dropdowns
$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY name");
$evaluators = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role='evaluator' ORDER BY name");
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title"><b><i class="fas fa-plus-circle"></i> Add New Feedback</b></h5>
        </div>
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label for="employee_id">Employee</label>
                    <select class="form-control" id="employee_id" name="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php while($row = $employees->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($_POST['employee_id']) && $_POST['employee_id'] == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo ucwords($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="evaluator_id">Evaluator</label>
                    <select class="form-control" id="evaluator_id" name="evaluator_id" required>
                        <option value="">Select Evaluator</option>
                        <?php while($row = $evaluators->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($_POST['evaluator_id']) && $_POST['evaluator_id'] == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo ucwords($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="feedback_type">Feedback Type</label>
                    <select class="form-control" id="feedback_type" name="feedback_type" required>
                        <option value="">Select Type</option>
                        <option value="positive" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'positive') ? 'selected' : ''; ?>>Positive</option>
                        <option value="constructive" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'constructive') ? 'selected' : ''; ?>>Constructive</option>
                        <option value="coaching" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'coaching') ? 'selected' : ''; ?>>Coaching</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="feedback_text">Feedback Text</label>
                    <textarea class="form-control" id="feedback_text" name="feedback_text" rows="5" required placeholder="Enter feedback details..."><?php echo isset($_POST['feedback_text']) ? htmlspecialchars($_POST['feedback_text']) : ''; ?></textarea>
                </div>
                
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Feedback</button>
                    <a href="index.php?page=feedback_history" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#feedback_type').change(function(){
            // Change background color based on feedback type
            var color = '';
            switch($(this).val()){
                case 'positive':
                    color = '#d4edda';
                    break;
                case 'constructive':
                    color = '#fff3cd';
                    break;
                case 'coaching':
                    color = '#d1ecf1';
                    break;
                default:
                    color = '';
            }
            $(this).css('background-color', color);
        });
        
        // Trigger change on page load if a value is already selected
        $('#feedback_type').trigger('change');
        
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
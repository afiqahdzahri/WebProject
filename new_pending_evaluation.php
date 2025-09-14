<?php
include 'db_connect.php';

// Handle form submission
if(isset($_POST['save_pending_evaluation'])){
    extract($_POST);
    
    // Validate required fields
    if(empty($employee_id) || empty($task_id) || empty($evaluator_id) || empty($due_date)){
        echo "<script>alert('Please fill all required fields.');</script>";
    } else {
        $data = "employee_id = '$employee_id'";
        $data .= ", task_id = '$task_id'";
        $data .= ", evaluator_id = '$evaluator_id'";
        $data .= ", evaluation_period = '$evaluation_period'";
        $data .= ", status = '$status'";
        $data .= ", due_date = '$due_date'";
        $data .= ", priority = '$priority'";
        $data .= ", evaluation_type = '$evaluation_type'";
        
        if(empty($id)){
            $save = $conn->query("INSERT INTO pending_evaluations SET $data");
        } else {
            $save = $conn->query("UPDATE pending_evaluations SET $data WHERE id = $id");
        }
        
        if($save){
            echo "<script>alert('Pending evaluation saved successfully.'); window.location.href='index.php?page=pending_evaluations';</script>";
        } else {
            echo "<script>alert('Error saving pending evaluation: ".$conn->error."');</script>";
        }
    }
}

// If editing, get existing record
$evaluation = array('id'=>'','employee_id'=>'','task_id'=>'','evaluator_id'=>'','evaluation_period'=>'Q2 2024','status'=>'pending','due_date'=>'','priority'=>'medium','evaluation_type'=>'task');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM pending_evaluations WHERE id = $id");
    if($qry->num_rows > 0){
        $evaluation = $qry->fetch_assoc();
    }
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title"><?php echo empty($evaluation['id']) ? 'Add New' : 'Edit'; ?> Pending Evaluation</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $evaluation['id']; ?>">
                
                <div class="form-group">
                    <label for="employee_id">Employee <span class="text-danger">*</span></label>
                    <select class="form-control" name="employee_id" id="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php
                        $employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name, employee_id FROM employee_list ORDER BY name");
                        while($row = $employees->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($evaluation['employee_id'] == $row['id']) ? 'selected' : '' ?>>
                            <?php echo $row['name'] . ' (' . $row['employee_id'] . ')' ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="task_id">Task <span class="text-danger">*</span></label>
                    <select class="form-control" name="task_id" id="task_id" required>
                        <option value="">Select Task</option>
                        <?php
                        $tasks = $conn->query("SELECT id, task FROM task_list ORDER BY task");
                        while($row = $tasks->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($evaluation['task_id'] == $row['id']) ? 'selected' : '' ?>>
                            <?php echo $row['task'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="evaluator_id">Evaluator <span class="text-danger">*</span></label>
                    <select class="form-control" name="evaluator_id" id="evaluator_id" required>
                        <option value="">Select Evaluator</option>
                        <?php
                        $evaluators = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role='evaluator' ORDER BY name");
                        while($row = $evaluators->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($evaluation['evaluator_id'] == $row['id']) ? 'selected' : '' ?>>
                            <?php echo $row['name'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="evaluation_period">Evaluation Period</label>
                    <input type="text" class="form-control" name="evaluation_period" id="evaluation_period" 
                           value="<?php echo $evaluation['evaluation_period']; ?>" placeholder="e.g., Q2 2024">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" id="status">
                        <option value="pending" <?php echo ($evaluation['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="submitted" <?php echo ($evaluation['status'] == 'submitted') ? 'selected' : '' ?>>Submitted</option>
                        <option value="overdue" <?php echo ($evaluation['status'] == 'overdue') ? 'selected' : '' ?>>Overdue</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Due Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="due_date" id="due_date" 
                           value="<?php echo $evaluation['due_date'] ? $evaluation['due_date'] : date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select class="form-control" name="priority" id="priority">
                        <option value="low" <?php echo ($evaluation['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?php echo ($evaluation['priority'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?php echo ($evaluation['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="evaluation_type">Evaluation Type</label>
                    <select class="form-control" name="evaluation_type" id="evaluation_type">
                        <option value="task" <?php echo ($evaluation['evaluation_type'] == 'task') ? 'selected' : '' ?>>Task</option>
                        <option value="periodic" <?php echo ($evaluation['evaluation_type'] == 'periodic') ? 'selected' : '' ?>>Periodic</option>
                        <option value="adhoc" <?php echo ($evaluation['evaluation_type'] == 'adhoc') ? 'selected' : '' ?>>Adhoc</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="save_pending_evaluation" class="btn btn-primary">Save Evaluation</button>
                    <a href="index.php?page=pending_evaluations" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Set default due date to 7 days from now if not set
    if($('#due_date').val() == '') {
        var nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        $('#due_date').val(nextWeek.toISOString().split('T')[0]);
    }
});
</script>
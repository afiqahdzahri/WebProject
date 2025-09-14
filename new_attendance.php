<?php
include 'db_connect.php';

// Handle form submission
if(isset($_POST['save_attendance'])){
    extract($_POST);
    
    // Validate required fields
    if(empty($employee_id) || empty($attendance_date) || empty($status)){
        echo "<script>alert('Please fill all required fields.');</script>";
    } else {
        // Format time values (set to 00:00:00 if not provided)
        $time_in = !empty($time_in) ? $time_in : '00:00:00';
        $time_out = !empty($time_out) ? $time_out : '00:00:00';
        
        $data = "employee_id = '$employee_id'";
        $data .= ", attendance_date = '$attendance_date'";
        $data .= ", time_in = '$time_in'";
        $data .= ", time_out = '$time_out'";
        $data .= ", status = '$status'";
        $data .= ", remarks = '$remarks'";
        
        if(empty($id)){
            $save = $conn->query("INSERT INTO attendance_records SET $data");
        } else {
            $save = $conn->query("UPDATE attendance_records SET $data WHERE id = $id");
        }
        
        if($save){
            echo "<script>alert('Attendance record saved successfully.'); window.location.href='index.php?page=attendance_records';</script>";
        } else {
            echo "<script>alert('Error saving attendance record: ".$conn->error."');</script>";
        }
    }
}

// If editing, get existing record
$attendance = array('id'=>'','employee_id'=>'','attendance_date'=>'','time_in'=>'','time_out'=>'','status'=>'present','remarks'=>'');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM attendance_records WHERE id = $id");
    if($qry->num_rows > 0){
        $attendance = $qry->fetch_assoc();
    }
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title"><?php echo empty($attendance['id']) ? 'Add New' : 'Edit'; ?> Attendance Record</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $attendance['id']; ?>">
                
                <div class="form-group">
                    <label for="employee_id">Employee <span class="text-danger">*</span></label>
                    <select class="form-control" name="employee_id" id="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php
                        $employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name, employee_id FROM employee_list ORDER BY name");
                        while($row = $employees->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($attendance['employee_id'] == $row['id']) ? 'selected' : '' ?>>
                            <?php echo $row['name'] . ' (' . $row['employee_id'] . ')' ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="attendance_date">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="attendance_date" id="attendance_date" 
                           value="<?php echo $attendance['attendance_date'] ? $attendance['attendance_date'] : date('Y-m-d'); ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="time_in">Time In</label>
                            <input type="time" class="form-control" name="time_in" id="time_in" 
                                   value="<?php echo ($attendance['time_in'] && $attendance['time_in'] != '00:00:00') ? substr($attendance['time_in'], 0, 5) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="time_out">Time Out</label>
                            <input type="time" class="form-control" name="time_out" id="time_out" 
                                   value="<?php echo ($attendance['time_out'] && $attendance['time_out'] != '00:00:00') ? substr($attendance['time_out'], 0, 5) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select class="form-control" name="status" id="status" required>
                        <option value="present" <?php echo ($attendance['status'] == 'present') ? 'selected' : '' ?>>Present</option>
                        <option value="absent" <?php echo ($attendance['status'] == 'absent') ? 'selected' : '' ?>>Absent</option>
                        <option value="late" <?php echo ($attendance['status'] == 'late') ? 'selected' : '' ?>>Late</option>
                        <option value="half_day" <?php echo ($attendance['status'] == 'half_day') ? 'selected' : '' ?>>Half Day</option>
                        <option value="leave" <?php echo ($attendance['status'] == 'leave') ? 'selected' : '' ?>>Leave</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="3"><?php echo $attendance['remarks']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="save_attendance" class="btn btn-primary">Save Attendance</button>
                    <a href="index.php?page=attendance_records" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Set default time values if not set
    if($('#time_in').val() == '') {
        $('#time_in').val('08:00');
    }
    if($('#time_out').val() == '') {
        $('#time_out').val('17:00');
    }
});
</script>
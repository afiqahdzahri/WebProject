<?php
include 'db_connect.php';

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $query = "SELECT a.*, e.firstname, e.lastname, e.employee_id 
              FROM attendance_records a 
              JOIN employee_list e ON a.employee_id = e.id 
              WHERE a.id = $id";
    
    $result = $conn->query($query);
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $status = isset($row['status']) ? $row['status'] : 'present';
        $badge_color = '';
        switch($status){
            case 'present':
                $badge_color = 'success';
                break;
            case 'absent':
                $badge_color = 'danger';
                break;
            case 'late':
                $badge_color = 'warning';
                break;
            case 'half_day':
                $badge_color = 'info';
                break;
            case 'leave':
                $badge_color = 'secondary';
                break;
            default:
                $badge_color = 'secondary';
        }
        
        // Format dates safely
        $attendance_date = isset($row['attendance_date']) ? date("M d, Y", strtotime($row['attendance_date'])) : 'N/A';
        $time_in = (isset($row['time_in']) && $row['time_in'] != '00:00:00') ? date("h:i A", strtotime($row['time_in'])) : 'N/A';
        $time_out = (isset($row['time_out']) && $row['time_out'] != '00:00:00') ? date("h:i A", strtotime($row['time_out'])) : 'N/A';
        $date_created = isset($row['date_created']) ? date("M d, Y H:i:s", strtotime($row['date_created'])) : 'N/A';
        $last_updated = (isset($row['last_updated']) && !empty($row['last_updated'])) ? date("M d, Y H:i:s", strtotime($row['last_updated'])) : 'N/A';
        $remarks = isset($row['remarks']) ? $row['remarks'] : 'N/A';
        
        echo '
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Employee ID:</b></label>
                    <p>'.(isset($row['employee_id']) ? $row['employee_id'] : 'N/A').'</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Employee Name:</b></label>
                    <p>'.(isset($row['firstname']) && isset($row['lastname']) ? ucwords($row['firstname'].' '.$row['lastname']) : 'N/A').'</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Attendance Date:</b></label>
                    <p>'.$attendance_date.'</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Status:</b></label>
                    <p><span class="badge badge-'.$badge_color.'">'.ucfirst(str_replace('_', ' ', $status)).'</span></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Time In:</b></label>
                    <p>'.$time_in.'</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Time Out:</b></label>
                    <p>'.$time_out.'</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><b>Remarks:</b></label>
                    <p>'.$remarks.'</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Date Created:</b></label>
                    <p>'.$date_created.'</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><b>Last Updated:</b></label>
                    <p>'.$last_updated.'</p>
                </div>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-danger">Attendance record not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
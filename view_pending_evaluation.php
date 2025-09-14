<?php
include 'db_connect.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    $query = "SELECT pe.*, 
              e.firstname as e_firstname, e.lastname as e_lastname, e.employee_id,
              t.task,
              u.firstname as u_firstname, u.lastname as u_lastname
              FROM pending_evaluations pe
              JOIN employee_list e ON pe.employee_id = e.id
              JOIN task_list t ON pe.task_id = t.id
              JOIN users u ON pe.evaluator_id = u.id
              WHERE pe.id = $id";
    
    $result = $conn->query($query);
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        
        $status = $row['status'];
        $status_badge = '';
        switch($status){
            case 'pending':
                $status_badge = 'warning';
                break;
            case 'submitted':
                $status_badge = 'success';
                break;
            case 'overdue':
                $status_badge = 'danger';
                break;
            default:
                $status_badge = 'secondary';
        }
        
        $priority = $row['priority'];
        $priority_badge = '';
        switch($priority){
            case 'high':
                $priority_badge = 'danger';
                break;
            case 'medium':
                $priority_badge = 'warning';
                break;
            case 'low':
                $priority_badge = 'info';
                break;
            default:
                $priority_badge = 'secondary';
        }
        
        echo '
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title">Pending Evaluation Details</h5>
                    <div class="card-tools">
                        <a href="index.php?page=pending_evaluations" class="btn btn-sm btn-default btn-flat border-primary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Employee:</b></label>
                                <p>'.ucwords($row['e_firstname'].' '.$row['e_lastname']).' ('.$row['employee_id'].')</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Task:</b></label>
                                <p>'.$row['task'].'</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Evaluator:</b></label>
                                <p>'.ucwords($row['u_firstname'].' '.$row['u_lastname']).'</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Evaluation Period:</b></label>
                                <p>'.$row['evaluation_period'].'</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><b>Status:</b></label>
                                <p><span class="badge badge-'.$status_badge.'">'.ucfirst($status).'</span></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><b>Due Date:</b></label>
                                <p>'.date("M d, Y", strtotime($row['due_date'])).'</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><b>Priority:</b></label>
                                <p><span class="badge badge-'.$priority_badge.'">'.ucfirst($priority).'</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Evaluation Type:</b></label>
                                <p>'.ucfirst(str_replace('_', ' ', $row['evaluation_type'])).'</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>Date Created:</b></label>
                                <p>'.date("M d, Y", strtotime($row['date_created'])).'</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-center">
                        <a href="index.php?page=edit_pending_evaluation&id='.$id.'" class="btn btn-primary">Edit Evaluation</a>
                        <a href="index.php?page=pending_evaluations" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-danger">Evaluation record not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
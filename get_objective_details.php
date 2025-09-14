<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    echo '<p>Please log in to view objective details</p>';
    exit;
}

include 'db_connect.php';

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // First get the basic objective data
    $query = $conn->query("SELECT 
        io.*, 
        CONCAT(el.firstname, ' ', el.lastname) as employee_name
        FROM improvement_objectives io
        LEFT JOIN employee_list el ON io.employee_id = el.id
        WHERE io.id = $id");
    
    if($query && $query->num_rows > 0) {
        $objective = $query->fetch_assoc();
        
        // Now try to get KPI metric name if kpi_metric_id exists
        $kpi_metric_name = 'N/A';
        if (isset($objective['kpi_metric_id']) && !empty($objective['kpi_metric_id'])) {
            $kpi_query = $conn->query("SELECT metric_name FROM kpi_metrics WHERE id = " . $objective['kpi_metric_id']);
            if ($kpi_query && $kpi_query->num_rows > 0) {
                $kpi_data = $kpi_query->fetch_assoc();
                $kpi_metric_name = $kpi_data['metric_name'];
            }
        }
        
        $status = $objective['status'];
        $badge_color = '';
        switch($status){
            case 'ongoing':
                $badge_color = 'warning';
                break;
            case 'completed':
                $badge_color = 'success';
                break;
            case 'overdue':
                $badge_color = 'danger';
                break;
            default:
                $badge_color = 'secondary';
        }
        
        echo '
        <div class="row">
            <div class="col-md-6">
                <p><strong>Employee:</strong> '.(isset($objective['employee_name']) ? $objective['employee_name'] : 'Unknown').'</p>
                <p><strong>KPI Metric:</strong> '.$kpi_metric_name.'</p>
                <p><strong>Current Value:</strong> '.(isset($objective['current_value']) ? $objective['current_value'] : 'N/A').'</p>
            </div>
            <div class="col-md-6">
                <p><strong>Target Value:</strong> '.(isset($objective['target_value']) ? $objective['target_value'] : 'N/A').'</p>
                <p><strong>Target Date:</strong> '.(isset($objective['target_date']) ? date("M d, Y", strtotime($objective['target_date'])) : 'N/A').'</p>
                <p><strong>Status:</strong> <span class="badge badge-'.$badge_color.'">'.ucfirst($status).'</span></p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <p><strong>Objective:</strong></p>
                <p>'.(isset($objective['objective_text']) ? $objective['objective_text'] : 'No objective text available').'</p>
            </div>
        </div>';
        
        // Show progress if available
        if (!empty($objective['progress_notes'])) {
            echo '
            <div class="row mt-3">
                <div class="col-12">
                    <p><strong>Progress Notes:</strong></p>
                    <p>'.$objective['progress_notes'].'</p>
                </div>
            </div>';
        }
    } else {
        echo '<div class="alert alert-danger">Objective not found</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request</div>';
}

// Close database connection
$conn->close();
?>
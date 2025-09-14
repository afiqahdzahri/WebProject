<?php
include 'db_connect.php';

// Set proper content type
header('Content-Type: text/html');

if(isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Fetch department details with manager and team lead names
    $query = "SELECT df.*, 
                     m.firstname as manager_firstname, m.lastname as manager_lastname,
                     tl.firstname as teamlead_firstname, tl.lastname as teamlead_lastname
              FROM department_formation df
              LEFT JOIN users m ON df.manager_id = m.id
              LEFT JOIN users tl ON df.team_lead_id = tl.id
              WHERE df.id = $id";
    
    $result = $conn->query($query);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $manager_name = isset($row['manager_firstname']) ? 
            $row['manager_firstname'] . ' ' . $row['manager_lastname'] : 'Unknown';
        
        $team_lead_name = (isset($row['teamlead_firstname']) && $row['team_lead_id']) ? 
            $row['teamlead_firstname'] . ' ' . $row['teamlead_lastname'] : 'Not Assigned';
        
        $date_created = isset($row['date_created']) ? 
            date("M d, Y", strtotime($row['date_created'])) : 'N/A';
        
        $description = isset($row['description']) && !empty($row['description']) ? 
            $row['description'] : 'No description provided';
        
        echo '
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Department Name:</b></label>
                        <p>'.htmlspecialchars($row['department_name'] ?? 'N/A').'</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Date Created:</b></label>
                        <p>'.$date_created.'</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Manager:</b></label>
                        <p>'.htmlspecialchars($manager_name).'</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Team Lead:</b></label>
                        <p>'.htmlspecialchars($team_lead_name).'</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><b>Description:</b></label>
                        <p>'.nl2br(htmlspecialchars($description)).'</p>
                    </div>
                </div>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-danger">Department formation not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request. Department ID not provided.</div>';
}
?>
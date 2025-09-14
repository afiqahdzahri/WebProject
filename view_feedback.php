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

// Determine badge color based on feedback type
$badge_color = '';
switch($feedback['feedback_type']){
    case 'positive':
        $badge_color = 'success';
        break;
    case 'constructive':
        $badge_color = 'warning';
        break;
    case 'coaching':
        $badge_color = 'info';
        break;
    default:
        $badge_color = 'secondary';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Feedback Details</h5>
                </div>
                <div class="card-body">
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
                            <th>Feedback Type</th>
                            <td><span class="badge badge-<?php echo $badge_color; ?>"><?php echo ucfirst($feedback['feedback_type']); ?></span></td>
                        </tr>
                        <tr>
                            <th>Feedback Date</th>
                            <td><?php echo date("M d, Y H:i", strtotime($feedback['feedback_date'])); ?></td>
                        </tr>
                        <tr>
                            <th>Feedback Text</th>
                            <td><?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
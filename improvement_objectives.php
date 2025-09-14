<?php
// Start output buffering to prevent headers already sent error
ob_start();

// Check if session is not already active before starting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    // Use JavaScript redirect if headers already sent
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

include 'db_connect.php';

// Handle delete action if requested
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = $conn->query("DELETE FROM improvement_objectives WHERE id = $delete_id");
    if ($delete_query) {
        $_SESSION['success_msg'] = "Objective successfully deleted";
    } else {
        $_SESSION['error_msg'] = "Error deleting objective";
    }
    
    // Redirect without JavaScript to avoid issues
    header("Location: improvement_objectives.php");
    exit;
}

// Handle success message from edit page
if (isset($_GET['edit_success']) && $_GET['edit_success'] == '1') {
    $_SESSION['success_msg'] = "Objective successfully updated";
}

// End output buffering and clean any unwanted output
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improvement Objectives</title>
    <!-- Include necessary CSS and JS files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Improvement Objectives</h3>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_improvement_objective"><i class="fa fa-plus"></i> Add New Improvement Objective</a>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Display success/error messages
                if (isset($_SESSION['success_msg'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success_msg'] . '</div>';
                    unset($_SESSION['success_msg']);
                }
                if (isset($_SESSION['error_msg'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error_msg'] . '</div>';
                    unset($_SESSION['error_msg']);
                }
                ?>
                <table class="table table-hover table-bordered" id="list">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Employee</th>
                            <th>Objective</th>
                            <th>KPI Metric</th>
                            <th>Current Value</th>
                            <th>Target Value</th>
                            <th>Target Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        // Fetch employee names
                        $employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list");
                        $emp_arr = array();
                        if ($employees) {
                            while($row = $employees->fetch_assoc()){
                                $emp_arr[$row['id']] = $row['name'];
                            }
                        }
                        
                        // Fetch KPI metrics for display
                        $kpi_metrics = $conn->query("SELECT id, metric_name FROM kpi_metrics");
                        $kpi_arr = array();
                        if ($kpi_metrics) {
                            while($row = $kpi_metrics->fetch_assoc()){
                                $kpi_arr[$row['id']] = $row['metric_name'];
                            }
                        }
                        
                        $qry = $conn->query("SELECT io.* FROM improvement_objectives io ORDER BY io.target_date DESC");
                        if($qry && $qry->num_rows > 0) {
                            while($row = $qry->fetch_assoc()):
                                $status = $row['status'];
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
                                
                                // Safely get KPI metric name
                                $kpi_metric_name = 'N/A';
                                if (isset($row['kpi_metric_id']) && !empty($row['kpi_metric_id']) && isset($kpi_arr[$row['kpi_metric_id']])) {
                                    $kpi_metric_name = $kpi_arr[$row['kpi_metric_id']];
                                }
                        ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
                            <td><b><?php echo strlen($row['objective_text']) > 30 ? substr($row['objective_text'], 0, 30) . '...' : $row['objective_text'] ?></b></td>
                            <td><b><?php echo $kpi_metric_name ?></b></td>
                            <td class="text-center"><b><?php echo $row['current_value'] ?></b></td>
                            <td class="text-center"><b><?php echo $row['target_value'] ?></b></td>
                            <td><b><?php echo date("M d, Y", strtotime($row['target_date'])) ?></b></td>
                            <td>
                                <span class="badge badge-<?php echo $badge_color ?>"><?php echo ucfirst($status) ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item view_objective" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="./index.php?page=edit_improvement_objective&id=<?php echo $row['id'] ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_objective" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                                </div>
                            </td>
                        </tr>	
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="9" class="text-center">No improvement objectives found</td></tr>';
                        }
                        ?>                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing objective details -->
<div class="modal fade" id="viewObjectiveModal" tabindex="-1" role="dialog" aria-labelledby="viewObjectiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewObjectiveModalLabel">Objective Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="objectiveDetails">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#list').dataTable();
        
        $('.view_objective').click(function(){
            var objectiveId = $(this).data('id');
            // Load objective details via AJAX
            $.ajax({
                url: 'get_objective_details.php',
                type: 'GET',
                data: {id: objectiveId},
                success: function(response) {
                    $('#objectiveDetails').html(response);
                    $('#viewObjectiveModal').modal('show');
                },
                error: function() {
                    alert('Error loading objective details');
                }
            });
        });
        
        $('.delete_objective').click(function(){
            var objectiveId = $(this).data('id');
            if(confirm("Are you sure you want to delete this improvement objective?")) {
                window.location.href = 'improvement_objectives.php?delete_id=' + objectiveId;
            }
        });
        
        // Close modal and refresh page when modal is hidden
        $('#viewObjectiveModal').on('hidden.bs.modal', function () {
            // You can add any cleanup code here if needed
        });
    });
</script>
</body>
</html>
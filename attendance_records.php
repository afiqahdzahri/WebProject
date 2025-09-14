<?php
include 'db_connect.php';

// Handle delete action
if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    $delete = $conn->query("DELETE FROM attendance_records WHERE id = $id");
    if($delete){
        echo "<script>alert('Attendance record deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
}

// Handle filter requests
$filter_employee = isset($_GET['employee']) ? $_GET['employee'] : '';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query with filters
$query = "SELECT a.*, e.firstname, e.lastname, e.employee_id 
          FROM attendance_records a 
          JOIN employee_list e ON a.employee_id = e.id 
          WHERE 1=1";

if(!empty($filter_employee)) {
    $query .= " AND a.employee_id = '$filter_employee'";
}
if(!empty($filter_date_from)) {
    $query .= " AND a.attendance_date >= '$filter_date_from'";
}
if(!empty($filter_date_to)) {
    $query .= " AND a.attendance_date <= '$filter_date_to'";
}
if(!empty($filter_status) && $filter_status != 'all') {
    $query .= " AND a.status = '$filter_status'";
}

$query .= " ORDER BY a.attendance_date DESC, a.time_in DESC";

$qry = $conn->query($query);
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title">Attendance Records</h5>
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_attendance_record">
                    <i class="fa fa-plus"></i> Add New Attendance Record
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="attendance_records">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="employee">Employee</label>
                                <select class="form-control" name="employee" id="employee">
                                    <option value="">All Employees</option>
                                    <?php
                                    $employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list ORDER BY name");
                                    while($row = $employees->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo ($filter_employee == $row['id']) ? 'selected' : '' ?>>
                                        <?php echo $row['name'] ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="date_from">From Date</label>
                                <input type="date" class="form-control" name="date_from" id="date_from" value="<?php echo $filter_date_from ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="date_to">To Date</label>
                                <input type="date" class="form-control" name="date_to" id="date_to" value="<?php echo $filter_date_to ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="all" <?php echo ($filter_status == 'all') ? 'selected' : '' ?>>All Status</option>
                                    <option value="present" <?php echo ($filter_status == 'present') ? 'selected' : '' ?>>Present</option>
                                    <option value="absent" <?php echo ($filter_status == 'absent') ? 'selected' : '' ?>>Absent</option>
                                    <option value="late" <?php echo ($filter_status == 'late') ? 'selected' : '' ?>>Late</option>
                                    <option value="half_day" <?php echo ($filter_status == 'half_day') ? 'selected' : '' ?>>Half Day</option>
                                    <option value="leave" <?php echo ($filter_status == 'leave') ? 'selected' : '' ?>>Leave</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="./index.php?page=attendance_records" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while($row = $qry->fetch_assoc()):
                        $status = $row['status'];
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
                    ?>
                    <tr>
                        <th class="text-center"><?php echo $i++ ?></th>
                        <td><b><?php echo $row['employee_id'] ?></b></td>
                        <td><b><?php echo ucwords($row['firstname'] . ' ' . $row['lastname']) ?></b></td>
                        <td><b><?php echo date("M d, Y", strtotime($row['attendance_date'])) ?></b></td>
                        <td><b><?php echo ($row['time_in'] != '00:00:00') ? date("h:i A", strtotime($row['time_in'])) : 'N/A' ?></b></td>
                        <td><b><?php echo ($row['time_out'] != '00:00:00') ? date("h:i A", strtotime($row['time_out'])) : 'N/A' ?></b></td>
                        <td>
                            <span class="badge badge-<?php echo $badge_color ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $status)) ?>
                            </span>
                        </td>
                        <td><b><?php echo $row['remarks'] ? $row['remarks'] : 'N/A' ?></b></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_attendance" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="./index.php?page=edit_attendance&id=<?php echo $row['id'] ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_attendance" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="viewAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewAttendanceModalLabel">Attendance Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="attendanceDetails">
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
    $('#list').dataTable({
        "pageLength": 25,
        "order": [[3, "desc"]]
    });
    
    // Set today's date as default for filter dates if not set
    if($('#date_from').val() == '') {
        var oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        $('#date_from').val(oneWeekAgo.toISOString().split('T')[0]);
    }
    
    if($('#date_to').val() == '') {
        $('#date_to').val(new Date().toISOString().split('T')[0]);
    }
    
    // View attendance record
    $('.view_attendance').click(function(){
        var id = $(this).attr('data-id');
        $.ajax({
            url: 'get_attendance_details.php',
            method: 'POST',
            data: {id: id},
            success: function(response) {
                $('#attendanceDetails').html(response);
                $('#viewAttendanceModal').modal('show');
            }
        });
    });
    
    // Delete attendance record
    $('.delete_attendance').click(function(){
        var id = $(this).attr('data-id');
        if(confirm("Are you sure to delete this attendance record?")) {
            window.location.href = 'index.php?page=attendance_records&delete_id=' + id;
        }
    });
});
</script>
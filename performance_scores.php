<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login_id']) || empty($_SESSION['login_id'])) {
    header("location: login.php");
    exit;
}

include 'db_connect.php';
?>
<div class="col-lg-12">
	<div class="card card-outline card-info">
		<div class="card-header">
			<h3 class="card-title">Performance Scores</h3>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./new_performance_score.php"><i class="fa fa-plus"></i> Add New Performance Score</a>
			</div>
		</div>
		<div class="card-body">
			<?php if(isset($_SESSION['success_msg'])): ?>
				<div class="alert alert-success"><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
			<?php endif; ?>
			
			<?php if(isset($_SESSION['error_msg'])): ?>
				<div class="alert alert-danger"><?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></div>
			<?php endif; ?>
			
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee</th>
						<th>Evaluation Period</th>
						<th>Productivity</th>
						<th>Attendance</th>
						<th>Utilization</th>
						<th>Quality</th>
						<th>Overall Score</th>
						<th>Evaluator</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					// Fetch employee names
					$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list");
					$emp_arr = array();
					while($row = $employees->fetch_assoc()){
						$emp_arr[$row['id']] = $row['name'];
					}
					
					// Fetch evaluator names
					$evaluators = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role='evaluator'");
					$eval_arr = array();
					while($row = $evaluators->fetch_assoc()){
						$eval_arr[$row['id']] = $row['name'];
					}
					
					$qry = $conn->query("SELECT ps.* FROM performance_scores ps ORDER BY ps.date_created DESC");
					while($row = $qry->fetch_assoc()):
						$overall_score = $row['overall_score'];
						$score_color = '';
						if($overall_score >= 90) {
							$score_color = 'success';
						} elseif($overall_score >= 80) {
							$score_color = 'primary';
						} elseif($overall_score >= 70) {
							$score_color = 'warning';
						} else {
							$score_color = 'danger';
						}
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo $row['evaluation_period'] ?></b></td>
						<td class="text-center"><b><?php echo $row['productivity_score'] ?></b></td>
						<td class="text-center"><b><?php echo $row['attendance_score'] ?></b></td>
						<td class="text-center"><b><?php echo $row['utilization_score'] ?></b></td>
						<td class="text-center"><b><?php echo $row['quality_score'] ?></b></td>
						<td class="text-center">
							<span class="badge badge-<?php echo $score_color ?>"><?php echo $row['overall_score'] ?></span>
						</td>
						<td><b><?php echo isset($eval_arr[$row['evaluator_id']]) ? ucwords($eval_arr[$row['evaluator_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['date_created'])) ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_score" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./edit_performance_score.php?id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_score" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable();
		
	$('.view_score').click(function(){
		uni_modal("<i class='fa fa-chart-line'></i> Performance Score Details","view_performance_score.php?id="+$(this).attr('data-id'))
	});
	
	$('.delete_score').click(function(){
		_conf("Are you sure to delete this performance score?","delete_performance_score",[$(this).attr('data-id')])
	});
	});
	
	function delete_performance_score($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_performance_score',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Performance score successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				} else {
					alert_toast("Error deleting performance score",'error')
					end_load()
				}
			}
		})
	}
</script>
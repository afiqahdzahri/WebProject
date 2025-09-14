<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Pending Evaluations</h5>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee</th>
						<th>Task</th>
						<th>Evaluation Period</th>
						<th>Due Date</th>
						<th>Priority</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$evaluator_id = $_SESSION['login_id'];
					
					// Fetch employee names
					$employees = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM employee_list");
					$emp_arr = array();
					while($row = $employees->fetch_assoc()){
						$emp_arr[$row['id']] = $row['name'];
					}
					
					// Fetch task names
					$tasks = $conn->query("SELECT id, task FROM task_list");
					$task_arr = array();
					while($row = $tasks->fetch_assoc()){
						$task_arr[$row['id']] = $row['task'];
					}
					
					$qry = $conn->query("SELECT pe.* FROM pending_evaluations pe WHERE pe.evaluator_id = '$evaluator_id' AND pe.status = 'pending' ORDER BY pe.due_date ASC");
					while($row = $qry->fetch_assoc()):
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
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo isset($task_arr[$row['task_id']]) ? $task_arr[$row['task_id']] : 'Unknown' ?></b></td>
						<td><b><?php echo $row['evaluation_period'] ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['due_date'])) ?></b></td>
						<td>
							<span class="badge badge-<?php echo $priority_badge ?>"><?php echo ucfirst($priority) ?></span>
						</td>
						<td class="text-center">
							<a href="./index.php?page=evaluate_employee&id=<?php echo $row['id'] ?>" class="btn btn-primary btn-sm btn-flat">Evaluate</a>
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
	});
</script>
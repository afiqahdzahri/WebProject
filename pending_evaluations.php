<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_pending_evaluation_record"><i class="fa fa-plus"></i> Add New Pending Evaluation</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee</th>
						<th>Task</th>
						<th>Evaluator</th>
						<th>Evaluation Period</th>
						<th>Status</th>
						<th>Due Date</th>
						<th>Priority</th>
						<th>Type</th>
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
					
					// Fetch task names
					$tasks = $conn->query("SELECT id, task FROM task_list");
					$task_arr = array();
					while($row = $tasks->fetch_assoc()){
						$task_arr[$row['id']] = $row['task'];
					}
					
					// Fetch evaluator names
					$evaluators = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role='evaluator'");
					$eval_arr = array();
					while($row = $evaluators->fetch_assoc()){
						$eval_arr[$row['id']] = $row['name'];
					}
					
					$qry = $conn->query("SELECT pe.* FROM pending_evaluations pe ORDER BY pe.due_date ASC, pe.priority DESC");
					while($row = $qry->fetch_assoc()):
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
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo isset($task_arr[$row['task_id']]) ? $task_arr[$row['task_id']] : 'Unknown' ?></b></td>
						<td><b><?php echo isset($eval_arr[$row['evaluator_id']]) ? ucwords($eval_arr[$row['evaluator_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo $row['evaluation_period'] ?></b></td>
						<td>
							<span class="badge badge-<?php echo $status_badge ?>"><?php echo ucfirst($status) ?></span>
						</td>
						<td><b><?php echo date("M d, Y", strtotime($row['due_date'])) ?></b></td>
						<td>
							<span class="badge badge-<?php echo $priority_badge ?>"><?php echo ucfirst($priority) ?></span>
						</td>
						<td><b><?php echo ucfirst(str_replace('_', ' ', $row['evaluation_type'])) ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['date_created'])) ?></b></td>
						<td class="text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
			                      Action
			                    </button>
			                    <div class="dropdown-menu">
			                      <a class="dropdown-item" href="./index.php?page=view_pending_evaluation&id=<?php echo $row['id'] ?>">View</a>
			                      <div class="dropdown-divider"></div>
			                      <a class="dropdown-item" href="./index.php?page=edit_pending_evaluation&id=<?php echo $row['id'] ?>">Edit</a>
			                      <div class="dropdown-divider"></div>
			                      <a class="dropdown-item mark_as_submitted" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Mark as Submitted</a>
			                      <div class="dropdown-divider"></div>
			                      <a class="dropdown-item delete_pending_evaluation" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
<script>
	$(document).ready(function(){
		$('#list').dataTable({
			"order": [[5, "asc"], [6, "asc"]] // Sort by status then due date
		});
		
		$('.mark_as_submitted').click(function(){
			var id = $(this).attr('data-id');
			if(confirm("Are you sure to mark this evaluation as submitted?")) {
				mark_as_submitted(id);
			}
		});
		
		$('.delete_pending_evaluation').click(function(){
			var id = $(this).attr('data-id');
			if(confirm("Are you sure to delete this pending evaluation?")) {
				delete_pending_evaluation(id);
			}
		});
	});
	
	function mark_as_submitted(id){
		// Create a simple loading indicator
		$('body').append('<div id="loading" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:9999;display:flex;align-items:center;justify-content:center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
		
		// Use form submission instead of AJAX
		$('body').append('<form id="tempForm" method="POST" action="mark_evaluation_submitted.php" style="display:none"><input type="hidden" name="id" value="' + id + '"></form>');
		$('#tempForm').submit();
	}
	
	function delete_pending_evaluation(id){
		// Create a simple loading indicator
		$('body').append('<div id="loading" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:9999;display:flex;align-items:center;justify-content:center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
		
		// Use form submission instead of AJAX
		$('body').append('<form id="tempForm" method="POST" action="delete_pending_evaluation.php" style="display:none"><input type="hidden" name="id" value="' + id + '"></form>');
		$('#tempForm').submit();
	}
</script>
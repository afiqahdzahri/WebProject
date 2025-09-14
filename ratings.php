<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-info">
		<div class="card-header">
			<h5 class="card-title">Evaluation Ratings</h5>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee</th>
						<th>Task</th>
						<th>Evaluation Period</th>
						<th>Efficiency</th>
						<th>Timeliness</th>
						<th>Quality</th>
						<th>Accuracy</th>
						<th>Date Evaluated</th>
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
					
					$qry = $conn->query("SELECT r.* FROM ratings r WHERE r.evaluator_id = '$evaluator_id' ORDER BY r.date_created DESC");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo isset($task_arr[$row['task_id']]) ? $task_arr[$row['task_id']] : 'Unknown' ?></b></td>
						<td><b><?php echo $row['evaluation_period'] ?></b></td>
						<td class="text-center"><b><?php echo $row['efficiency'] ?>/5</b></td>
						<td class="text-center"><b><?php echo $row['timeliness'] ?>/5</b></td>
						<td class="text-center"><b><?php echo $row['quality'] ?>/5</b></td>
						<td class="text-center"><b><?php echo $row['accuracy'] ?>/5</b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['date_created'])) ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_rating" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View Details</a>
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
		
	$('.view_rating').click(function(){
		uni_modal("<i class='fa fa-chart-bar'></i> Rating Details","view_rating.php?id="+$(this).attr('data-id'))
	});
	});
</script>
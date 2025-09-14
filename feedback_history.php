<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_feedback"><i class="fa fa-plus"></i> Add New Feedback</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee</th>
						<th>Evaluator</th>
						<th>Feedback Type</th>
						<th>Feedback Text</th>
						<th>Feedback Date</th>
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
					
					$qry = $conn->query("SELECT fh.* FROM feedback_history fh ORDER BY fh.feedback_date DESC");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo isset($emp_arr[$row['employee_id']]) ? ucwords($emp_arr[$row['employee_id']]) : 'Unknown' ?></b></td>
						<td><b><?php echo isset($eval_arr[$row['evaluator_id']]) ? ucwords($eval_arr[$row['evaluator_id']]) : 'Unknown' ?></b></td>
						<td>
							<?php 
							$type = $row['feedback_type'];
							$badge_color = '';
							switch($type){
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
							<span class="badge badge-<?php echo $badge_color ?>"><?php echo ucfirst($type) ?></span>
						</td>
						<td><b><?php echo strlen($row['feedback_text']) > 50 ? substr($row['feedback_text'], 0, 50) . '...' : $row['feedback_text'] ?></b></td>
						<td><b><?php echo date("M d, Y H:i", strtotime($row['feedback_date'])) ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_feedback" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_feedback&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_feedback" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
		
	$('.view_feedback').click(function(){
		uni_modal("<i class='fa fa-comment'></i> Feedback Details","view_feedback.php?id="+$(this).attr('data-id'))
	});
	
	$('.delete_feedback').click(function(){
		_conf("Are you sure to delete this feedback?","delete_feedback",[$(this).attr('data-id')])
	});
	});
	
	function delete_feedback($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_feedback',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Feedback successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
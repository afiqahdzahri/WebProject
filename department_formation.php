<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Department Formation</h5>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_department_formation"><i class="fa fa-plus"></i> Add New Department Formation</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Department Name</th>
						<th>Manager</th>
						<th>Team Lead</th>
						<th>Description</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					// Fetch manager and team lead names
					$users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users");
					$user_arr = array();
					if($users) {
						while($row = $users->fetch_assoc()){
							$user_arr[$row['id']] = $row['name'];
						}
					}
					
					// Query department formations
					$qry = $conn->query("SELECT df.* FROM department_formation df ORDER BY df.date_created DESC");
					
					if($qry && $qry->num_rows > 0) {
						while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['department_name']) ?></b></td>
						<td><b><?php echo isset($user_arr[$row['manager_id']]) ? $user_arr[$row['manager_id']] : 'Unknown' ?></b></td>
						<td><b><?php echo isset($user_arr[$row['team_lead_id']]) ? $user_arr[$row['team_lead_id']] : 'Not Assigned' ?></b></td>
						<td><b><?php echo $row['description'] ? $row['description'] : 'No description' ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['date_created'])) ?></b></td>
						<td class="text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu">
		                      <a class="dropdown-item view_department_formation" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_department_formation&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_department_formation" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                    </div>
							</div>
						</td>
					</tr>	
					<?php endwhile; 
					} else {
						echo '<tr><td colspan="7" class="text-center">No department formations found</td></tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="viewDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewDepartmentModalLabel">Department Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="departmentDetails">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Loading department details...</p>
                </div>
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
		
		// View department formation
		$('.view_department_formation').click(function(){
			var id = $(this).attr('data-id');
			$('#departmentDetails').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p>Loading department details...</p></div>');
			$('#viewDepartmentModal').modal('show');
			
			$.ajax({
				url: 'ajax.php?action=get_department_details',
				method: 'POST',
				data: {id: id},
				success: function(response) {
					$('#departmentDetails').html(response);
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', status, error);
					$('#departmentDetails').html('<div class="alert alert-danger">Error loading department details. Please try again.</div>');
				}
			});
		});
		
		// Delete department formation
		$('.delete_department_formation').click(function(){
			var id = $(this).attr('data-id');
			_conf("Are you sure to delete this department formation?","delete_department_formation",[id]);
		});
	});
	
	function delete_department_formation(id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_department_formation',
			method:'POST',
			data:{id:id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success');
					setTimeout(function(){
						location.reload();
					},1500);
				} else {
					alert_toast("Error: " + resp,'error');
					end_load();
				}
			},
			error: function(xhr, status, error) {
				alert_toast("Error deleting department: " + error, 'error');
				end_load();
			}
		});
	}
</script>
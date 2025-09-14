<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title"><?php echo !isset($id) ? "Add New Department Formation" : "Edit Department Formation" ?></h5>
		</div>
		<div class="card-body">
			<form action="ajax.php?action=save_department_formation" id="manage-department">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_name" class="control-label">Department Name</label>
							<input type="text" class="form-control" id="department_name" name="department_name" value="<?php echo isset($department_name) ? $department_name : '' ?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="manager_id" class="control-label">Manager</label>
							<select class="form-control select2" id="manager_id" name="manager_id" required>
								<option value=""></option>
								<?php 
								$managers = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role = 'admin' OR role = 'evaluator'");
								while($row = $managers->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && $manager_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="team_lead_id" class="control-label">Team Lead (Optional)</label>
							<select class="form-control select2" id="team_lead_id" name="team_lead_id">
								<option value=""></option>
								<?php 
								$team_leads = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE role = 'evaluator' OR role = 'employee'");
								while($row = $team_leads->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($team_lead_id) && $team_lead_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="description" class="control-label">Description</label>
							<textarea name="description" id="description" cols="30" rows="3" class="form-control"><?php echo isset($description) ? $description : '' ?></textarea>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-sm btn-primary" form="manage-department">Save</button>
					<a class="btn btn-sm btn-default" href="index.php?page=department_formation">Cancel</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('.select2').select2({
		placeholder: "Please select here",
		width: "100%"
	});
	
	$('#manage-department').submit(function(e){
		e.preventDefault();
		start_load();
		$.ajax({
			url: $(this).attr('action'),
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved.",'success');
					setTimeout(function(){
						location.href = 'index.php?page=department_formation';
					},1500);
				} else {
					alert_toast("Error: " + resp,'error');
					end_load();
				}
			}
		});
	});
</script>
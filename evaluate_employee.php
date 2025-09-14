<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Employee Evaluation</h5>
		</div>
		<div class="card-body">
			<?php
			if(isset($_GET['id'])){
				$evaluation_id = $_GET['id'];
				$evaluation = $conn->query("SELECT pe.*, e.firstname, e.lastname, t.task 
										   FROM pending_evaluations pe 
										   JOIN employee_list e ON pe.employee_id = e.id 
										   JOIN task_list t ON pe.task_id = t.id 
										   WHERE pe.id = $evaluation_id")->fetch_assoc();
										   
				if($evaluation){
			?>
			<form id="evaluation-form">
				<input type="hidden" name="pending_evaluation_id" value="<?php echo $evaluation_id ?>">
				<input type="hidden" name="employee_id" value="<?php echo $evaluation['employee_id'] ?>">
				<input type="hidden" name="task_id" value="<?php echo $evaluation['task_id'] ?>">
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Employee</label>
							<input type="text" class="form-control" value="<?php echo $evaluation['firstname'] . ' ' . $evaluation['lastname'] ?>" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Task</label>
							<input type="text" class="form-control" value="<?php echo $evaluation['task'] ?>" readonly>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label>Efficiency (1-5)</label>
							<select class="form-control" name="efficiency" required>
								<option value="">Select Rating</option>
								<?php for($i=1; $i<=5; $i++): ?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Timeliness (1-5)</label>
							<select class="form-control" name="timeliness" required>
								<option value="">Select Rating</option>
								<?php for($i=1; $i<=5; $i++): ?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Quality (1-5)</label>
							<select class="form-control" name="quality" required>
								<option value="">Select Rating</option>
								<?php for($i=1; $i<=5; $i++): ?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Accuracy (1-5)</label>
							<select class="form-control" name="accuracy" required>
								<option value="">Select Rating</option>
								<?php for($i=1; $i<=5; $i++): ?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label>Remarks</label>
					<textarea class="form-control" name="remarks" rows="3" required></textarea>
				</div>
				
				<div class="form-group">
					<label>Overall Comment</label>
					<textarea class="form-control" name="overall_comment" rows="3" required></textarea>
				</div>
				
				<button type="submit" class="btn btn-primary">Submit Evaluation</button>
			</form>
			<?php
				} else {
					echo "<div class='alert alert-danger'>Evaluation not found.</div>";
				}
			} else {
				echo "<div class='alert alert-danger'>Invalid evaluation ID.</div>";
			}
			?>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#evaluation-form').submit(function(e){
		e.preventDefault();
		start_load();
		
		$.ajax({
			url: 'ajax.php?action=save_evaluation',
			method: 'POST',
			data: $(this).serialize(),
			success: function(resp){
				if(resp == 1){
					alert_toast('Evaluation submitted successfully', 'success');
					setTimeout(function(){
						location.href = 'index.php?page=evaluation';
					}, 2000);
				} else {
					alert_toast('Error submitting evaluation', 'error');
					end_load();
				}
			}
		});
	});
});
</script>
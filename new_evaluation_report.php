<?php 
include 'db_connect.php';
// Get current user ID (you may need to adjust this based on your authentication system)
$current_user_id = $_SESSION['login_id'] ?? 1; // Default to 1 if not set

if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM evaluation_reports WHERE id = ".$_GET['id']);
	foreach($qry->fetch_array() as $k => $val){
		$$k = $val;
	}
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card card-outline card-primary">
			<div class="card-header">
				<h3 class="card-title"><?php echo isset($id) ? 'Edit' : 'Generate New' ?> Evaluation Report</h3>
			</div>
			<div class="card-body">
				<form action="" id="manage-evaluation-report">
					<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
					<input type="hidden" name="generated_by" value="<?php echo $current_user_id ?>">
					
					<div class="form-group">
						<label for="report_name" class="control-label">Report Name</label>
						<input type="text" class="form-control" name="report_name" id="report_name" value="<?php echo isset($report_name) ? $report_name : '' ?>" required>
					</div>
					
					<div class="form-group">
						<label for="report_type" class="control-label">Report Type</label>
						<select class="form-control" name="report_type" id="report_type" required>
							<option value="">Select Report Type</option>
							<option value="performance" <?php echo isset($report_type) && $report_type == 'performance' ? 'selected' : '' ?>>Performance Evaluation</option>
							<option value="department" <?php echo isset($report_type) && $report_type == 'department' ? 'selected' : '' ?>>Department Summary</option>
							<option value="quarterly" <?php echo isset($report_type) && $report_type == 'quarterly' ? 'selected' : '' ?>>Quarterly Review</option>
							<option value="annual" <?php echo isset($report_type) && $report_type == 'annual' ? 'selected' : '' ?>>Annual Report</option>
						</select>
					</div>
					
					<div class="form-group">
						<label for="period" class="control-label">Period (e.g., Q1 2023, Jan 2023, etc.)</label>
						<input type="text" class="form-control" name="period" id="period" value="<?php echo isset($period) ? $period : '' ?>" required>
					</div>
					
					<div class="form-group">
						<label for="file_path" class="control-label">File Path/URL</label>
						<input type="text" class="form-control" name="file_path" id="file_path" value="<?php echo isset($file_path) ? $file_path : '' ?>" required>
						<small class="form-text text-muted">Enter the path where the report will be saved or the URL to access it</small>
					</div>
					
					<div class="form-group">
						<label for="description" class="control-label">Description/Notes</label>
						<textarea name="description" id="description" cols="30" rows="3" class="form-control"><?php echo isset($description) ? $description : '' ?></textarea>
					</div>
				</form>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-sm btn-primary" form="manage-evaluation-report"><i class="fa fa-save"></i> Save</button>
						<a class="btn btn-sm btn-default" href="index.php?page=evaluation_reports">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('#manage-evaluation-report').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_evaluation_report',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.href = 'index.php?page=evaluation_reports'
					},1500)
				} else {
					alert_toast("Error: " + resp,'error')
					end_load()
				}
			}
		})
	})
</script>
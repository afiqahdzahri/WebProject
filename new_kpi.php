<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM kpi_metrics WHERE id = ".$_GET['id']);
	foreach($qry->fetch_array() as $k => $val){
		$$k = $val;
	}
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card card-outline card-info">
			<div class="card-header">
				<h3 class="card-title">Add New KPI Metric</h3>
			</div>
			<div class="card-body">
				<form action="" id="manage-kpi">
					<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
					<div class="form-group">
						<label for="metric_name" class="control-label">Metric Name</label>
						<input type="text" class="form-control" name="metric_name" id="metric_name" value="<?php echo isset($metric_name) ? $metric_name : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="description" class="control-label">Description</label>
						<textarea name="description" id="description" cols="30" rows="2" class="form-control" required><?php echo isset($description) ? $description : '' ?></textarea>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="target_value" class="control-label">Target Value</label>
								<input type="number" step="0.01" class="form-control" name="target_value" id="target_value" value="<?php echo isset($target_value) ? $target_value : '' ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="min_value" class="control-label">Minimum Value</label>
								<input type="number" step="0.01" class="form-control" name="min_value" id="min_value" value="<?php echo isset($min_value) ? $min_value : '' ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="max_value" class="control-label">Maximum Value</label>
								<input type="number" step="0.01" class="form-control" name="max_value" id="max_value" value="<?php echo isset($max_value) ? $max_value : '' ?>" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="is_higher_better" class="control-label">Higher Value is Better?</label>
						<select class="form-control" name="is_higher_better" id="is_higher_better" required>
							<option value="1" <?php echo isset($is_higher_better) && $is_higher_better == 1 ? 'selected' : '' ?>>Yes</option>
							<option value="0" <?php echo isset($is_higher_better) && $is_higher_better == 0 ? 'selected' : '' ?>>No</option>
						</select>
					</div>
				</form>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-sm btn-primary" form="manage-kpi"><i class="fa fa-save"></i> Save</button>
						<a class="btn btn-sm btn-default" href="index.php?page=kpi_metrics">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('#manage-kpi').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_kpi',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.href = 'index.php?page=kpi_metrics'
					},1500)
				} else {
					alert_toast("Error: " + resp,'error')
					end_load()
				}
			}
		})
	})
</script>
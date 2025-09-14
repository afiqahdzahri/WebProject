<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-info">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_kpi"><i class="fa fa-plus"></i> Add New KPI Metric</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Metric Name</th>
						<th>Description</th>
						<th>Target Value</th>
						<th>Min Value</th>
						<th>Max Value</th>
						<th>Higher is Better</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM kpi_metrics ORDER BY metric_name ASC");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['metric_name']) ?></b></td>
						<td><b><?php echo $row['description'] ?></b></td>
						<td class="text-center"><b><?php echo $row['target_value'] ?></b></td>
						<td class="text-center"><b><?php echo $row['min_value'] ?></b></td>
						<td class="text-center"><b><?php echo $row['max_value'] ?></b></td>
						<td class="text-center">
							<?php if($row['is_higher_better']): ?>
								<span class="badge badge-success">Yes</span>
							<?php else: ?>
								<span class="badge badge-danger">No</span>
							<?php endif; ?>
						</td>
						<td><b><?php echo date("M d, Y", strtotime($row['date_created'])) ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item" href="./index.php?page=edit_kpi&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_kpi" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
	$('.delete_kpi').click(function(){
		_conf("Are you sure to delete this KPI metric?","delete_kpi",[$(this).attr('data-id')])
	})
	})
	function delete_kpi($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_kpi',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
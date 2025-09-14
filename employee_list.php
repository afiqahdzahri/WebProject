<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<h5 class="card-title">Assigned Employees</h5>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Department</th>
						<th>Designation</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$evaluator_id = $_SESSION['login_id'];
					
					$designations = $conn->query("SELECT * FROM designation_list ");
					$design_arr[0]= "Unset";
					while($row=$designations->fetch_assoc()){
						$design_arr[$row['id']] =$row['designation'];
					}
					
					$departments = $conn->query("SELECT * FROM department_list ");
					$dept_arr[0]= "Unset";
					while($row=$departments->fetch_assoc()){
						$dept_arr[$row['id']] =$row['department'];
					}
					
					$qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM employee_list WHERE evaluator_id = '$evaluator_id' order by concat(lastname,', ',firstname,' ',middlename) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['employee_id'] ?></b></td>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td><b><?php echo isset($dept_arr[$row['department_id']]) ? $dept_arr[$row['department_id']] : 'Unknown Department' ?></b></td>
						<td><b><?php echo isset($design_arr[$row['designation_id']]) ? $design_arr[$row['designation_id']] : 'Unknown Designation' ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_employee" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=evaluate_employee&emp_id=<?php echo $row['id'] ?>">Evaluate</a>
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
		$('#list').dataTable()
	$('.view_employee').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> Employee Details","view_employee.php?id="+$(this).attr('data-id'))
	})
	})
</script>
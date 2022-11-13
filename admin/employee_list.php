<?php 
include('../class/User.php');
$user = new User();
$user->adminLoginStatus();
include('include/header.php');
?>
<title>employee list</title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/employee.js"></script>	
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Employees List</h2>	
	<?php include 'menus.php'; ?>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
		<a href="#"><strong><span class="fa fa-dashboard"></span> Employee List</strong></a>
		<hr>		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<!-- <div class="col-md-2" align="right">
					<button type="button" name="add" id="addUser" class="btn btn-success btn-xs">Add</button>
				</div> -->
			</div>
		</div>
		<table id="employeeList" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Mobile</th>
					<th>Role</th>
                    <th>City</th>					
					<th></th>
					<!-- <th></th> -->
					
				</tr>
			</thead>
		</table>
	</div>
	<div id="employeeModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="employeeForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit Employee</h4>
    				</div>
    				<div class="modal-body">
						<div class="form-group">
							<label for="fullname" class="control-label"> Name*</label>
							<input type="text" class="form-control" id="fullname" name="fullname" placeholder="Name" required>							
						</div>
							   	
						<div class="form-group">
							<label for="lastname" class="control-label">Email*</label>							
							<input type="text" class="form-control"  id="email" name="email" placeholder="Email" required>							
						</div>
                        <div class="form-group">
							<label for="lastname" class="control-label">Mobile</label>							
							<input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Mobile">							
						</div>	 
						<div class="form-group">
							<label for="lastname" class="control-label">Job Role</label>							
							<input type="text" class="form-control" id="jobrole" name="jobrole" placeholder="Job Role">							
						</div>	 
						<div class="form-group" id="passwordSection">
							<label for="lastname" class="control-label">City</label>							
							<input type="text" class="form-control"  id="city" name="city" placeholder="City" required>							
						</div>
                        <div class="form-group">
							<label for="lastname" class="control-label">Address</label>							
							<input type="text" class="form-control" id="address" name="address" placeholder="Address">							
						</div>
						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="employeeid" id="employeeid" />
    					<input type="hidden" name="action" id="action" value="updateEmployee" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
</div>	
<?php include('include/footer.php');?>
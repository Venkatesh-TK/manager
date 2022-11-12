<?php 
include('../class/User.php');
$user = new User();
$user->adminLoginStatus();
include('include/header.php');
$con = mysqli_connect("127.0.0.1:4306","root","","test2");


	$sql = "SELECT * FROM request WHERE managerid ='".$_SESSION['adminUserid']."'";
	$Sql_query = mysqli_query($con,$sql);
	$All_request = mysqli_fetch_all($Sql_query,MYSQLI_ASSOC);
?>
<title>webdamn.com : Demo User Management System with PHP & MySQL</title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/request.js"></script>	
<link rel="stylesheet" href="css/style.css">
<style>
    th, td{
        text-align:center;
    }
    .btn{
			background-color: red;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			cursor: pointer;
			border-radius: 20px;
		}
		.green{
			background-color: #00A170;
		}
		.red{
			background-color:  #DD4124;
		}
        .approved{
            background-color: green;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			border-radius: 15px;
        }
        .rejected{
            background-color: red;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			border-radius: 15px;
        }
        .resolved{
            background-color: orange;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			border-radius: 15px;
        }
        .itreject{
            background-color: #363945;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			border-radius: 15px;
        }
        .pending{
            background-color: #926AA6;
			border: none;
			color: white;
			padding: 5px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 15px;
			margin: 4px 2px;
			border-radius: 15px;
        }

    </style>
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Example: User Management System with PHP & MySQL</h2>	
	<?php include 'menus.php'; ?>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
		<a href="#"><strong><span class="fa fa-dashboard"></span> Request List</strong></a>
		<hr>		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				
			</div>
		</div>
		<table id="newtable" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Status</th>
					<th>Employee Name</th>
					<th>Company</th>
					<th>Asset Type</th>
					<th>Asset Detail</th>					
					<th></th>
					<!-- <th></th> -->
					
				</tr>
			</thead>
           <?php foreach ($All_request as $request) { ?>
			<tr>
				<td><?php echo $request['id']; ?></td>
                <td><?php echo $request['status']; ?></td>
                <td><?php echo $request['employee_name']; ?></td>
                <td><?php echo $request['company']; ?></td>
                <td><?php echo $request['asset_type']; ?></td>
                <td><?php echo $request['asset_details']; ?></td>
                <td>
					<?php
					if($request['status']=="1"){
						// if a course is active i.e. status is 1
						// the toggle button must be able to deactivate
						// we echo the hyperlink to the page "deactivate.php"
						// in order to make it look like a button
						// we use the appropriate css
						// red-deactivate
						// green- activate
                        echo
                        "<a href=activate.php?id=".$request['id']." class='btn green'>Approve</a>";
						echo
                        "<a href=deactivate.php?id=".$request['id']." class='btn red'>Reject</a>";}

					else if($request['status']=="2")
						echo  "<p class=approved >Approved</p>";
                    else if($request['status']=="3")
						echo "<p class=rejected >Rejected</p>";
                    else if($request['status']=="4")
						echo "<p class=resolved >Resolved</p>";
                    else if($request['status']=="5")
						echo "<p class=itreject >IT Team Rejected</p>";
                    else
                    echo "<p class=pending >Status Pending</p>";
					?>
			</tr>
		<?php
				} ?>

		</table>
	</div>
	<div id="userModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="userForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit User</h4>
    				</div>
    				<div class="modal-body">
						<div class="form-group">
							<label for="firstname" class="control-label">First Name*</label>
							<input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>							
						</div>
						<div class="form-group">
							<label for="lastname" class="control-label">Last Name</label>							
							<input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name">							
						</div>	   	
						<div class="form-group">
							<label for="lastname" class="control-label">Email*</label>							
							<input type="text" class="form-control"  id="email" name="email" placeholder="Email" required>							
						</div>	 
						<div class="form-group" id="passwordSection">
							<label for="lastname" class="control-label">Password*</label>							
							<input type="password" class="form-control"  id="password" name="password" placeholder="Password" required>							
						</div>
						<div class="form-group">
							<label for="gender" class="control-label">Gender</label>							
							<label class="radio-inline">
								<input type="radio" name="gender" id="male" value="male" required>Male
							</label>;
							<label class="radio-inline">
								<input type="radio" name="gender" id="female" value="female" required>Female
							</label>							
						</div>	
						<div class="form-group">
							<label for="lastname" class="control-label">Mobile</label>							
							<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile">							
						</div>	 
						<div class="form-group">
							<label for="lastname" class="control-label">Designation</label>							
							<input type="text" class="form-control" id="designation" name="designation" placeholder="designation">							
						</div>	
						<div class="form-group">
							<label for="gender" class="control-label">Status</label>							
							<label class="radio-inline">
								<input type="radio" name="status" id="active" value="active" required>Active
							</label>;
							<label class="radio-inline">
								<input type="radio" name="status" id="pending" value="pending" required>Pending
							</label>							
						</div>
						<div class="form-group">
							<label for="user_type" class="control-label">User Type</label>							
							<label class="radio-inline">
								<input type="radio" name="user_type" id="general" value="general" required>General
							</label>;
							<label class="radio-inline">
								<input type="radio" name="user_type" id="administrator" value="administrator" required>Administrator
							</label>							
						</div>	
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="userid" id="userid" />
    					<input type="hidden" name="action" id="action" value="updateUser" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
</div>	
<?php include('include/footer.php');?>
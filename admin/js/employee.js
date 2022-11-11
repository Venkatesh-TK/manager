$(document).ready(function(){
	var usersData = $('#employeeList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"action.php",
			type:"POST",
			data:{action:'listEmployee'},
			dataType:"json"
		},
		"columnDefs":[
			{
				// "targets":[0, 7, 8],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var employeeid = $(this).attr("id");		
		var action = "userDelete";
		if(confirm("Are you sure you want to delete this user?")) {
			$.ajax({
				url:"action.php",
				method:"POST",
				data:{employeeid:employeeid, action:action},
				success:function(data) {					
					usersData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	$('#addEmployee').click(function(){
		$('#employeeModal').modal('show');
		$('#employeeForm')[0].reset();
		$('#passwordSection').show();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add User");
		$('#action').val('addEmployee');
		$('#save').val('Add User');
	});	
	$(document).on('click', '.update', function(){
		var employeeid = $(this).attr("id");
		var action = 'getEmployee';
		$.ajax({
			url:'action.php',
			method:"POST",
			data:{employeeid:employeeid, action:action},
			dataType:"json",
			success:function(data){
				$('#employeeModal').modal('show');
				$('#employeeid').val(data.id);
				$('#fullname').val(data.fullname);
				$('#email').val(data.email);
				$('#mobile_number').val(data.mobile_number);
				$('#jobrole').val(data.jobrole);
				// $('#passwordSection').hide();
				// if(data.gender == 'male') {
				// 	$('#male').prop("checked", true);
				// } else if(data.gender == 'female') {
				// 	$('#female').prop("checked", true);
				// }
				// if(data.status == 'active') {
				// 	$('#active').prop("checked", true);
				// } else if(data.gender == 'pending') {
				// 	$('#pending').prop("checked", true);
				// }
				// if(data.type == 'general') {
				// 	$('#general').prop("checked", true);
				// } else if(data.type == 'administrator') {
				// 	$('#administrator').prop("checked", true);
				// }
				$('#city').val(data.city);
				$('#address').val(data.address);	
				$('.modal-title').html("<i class='fa fa-plus'></i> Edit Employee");
				$('#action').val('updateEmployee');
				$('#save').val('Save');
			}
		})
	});	
	$(document).on('submit','#employeeForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#employeeForm')[0].reset();
				$('#employeeModal').modal('hide');				
				$('#save').attr('disabled', false);
				usersData.ajax.reload();
			}
		})
	});	
});
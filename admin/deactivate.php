<?php
    include('details.php');

	// Connect to database
	$con=mysqli_connect($host,$username,$password,$database);

    include('../class/User.php');
    $user = new User();
    $user->adminLoginStatus();
    include('include/header.php');
    
     ?>
    <title>request list</title>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>		
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
    <script src="js/request.js"></script>	
    <link rel="stylesheet" href="css/style.css">
    <?php include('include/container.php');?>
    <div class="container contact">	
	<h2>Request List from Employees</h2>	
	<?php include 'menus.php';
     ?>
    
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
		<a href="#"><strong><span class="fa fa-dashboard"></span> Reason for Reject</strong></a>
		<hr>		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				
			</div>
		</div>
		<form method="POST" > 
            <div>
                <textarea name="rejectcomments" id="rejectcomments" cols="60" rows="10"></textarea>
            </div>
            <div class="btn_update">
                <input type="submit" name="btn_update" id="btn_update" class="btn btn-info" value="Submit" />
            </div>
        </form>
    </div>    
        <?php 
           
          $ids= $_GET['id'];
         
          if(isset($_POST['btn_update'])){

            $course_id=$_GET['id'];
            $rejectcomments = $_POST['rejectcomments'];
            $updateQuery = "UPDATE request 
			SET rejectcomments = '".$_POST["rejectcomments"]."', `status`= 3
			WHERE id={$ids}";
            $isUpdated = mysqli_query($con, $updateQuery);
           // header("location:requestlist.php"); 
            // if (isset($_GET['id']))
            // {
            //     // $course_id=$_GET['id'];
        
            //     $sql="UPDATE `request` SET
            //         `status`= 3 WHERE id='$course_id'";
        
                // Execute the query
                mysqli_query($con,$sql);
                echo "<script>
                window.location.href = 'requestlist.php';
                
        </script>";
                
                
            }  

            

    // }
     
	

?>






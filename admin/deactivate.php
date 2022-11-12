<?php

	// Connect to database
	$con=mysqli_connect("127.0.0.1:4306","root","","test2");

    include('../class/User.php');
    $user = new User();
    $user->adminLoginStatus();
    include('include/header.php');
    // $con = mysqli_connect("127.0.0.1:4306","root","","test2");
    
    
    //     $sql = "SELECT * FROM `request`";
    //     $Sql_query = mysqli_query($con,$sql);
    //     $All_request = mysqli_fetch_all($Sql_query,MYSQLI_ASSOC);
     ?>
    <title>webdamn.com : Demo User Management System with PHP & MySQL</title>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>		
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
    <script src="js/request.js"></script>	
    <link rel="stylesheet" href="css/style.css">
    <?php include('include/container.php');?>
    <div class="container contact">	
	<h2>Example: User Management System with PHP & MySQL</h2>	
	<?php include 'menus.php'; ?>
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
                <textarea name="reject_comments" id="reject_comments" cols="60" rows="10"></textarea>
            </div>
            <div class="btn_update">
                <input type="submit" name="btn_update" id="btn_update" class="btn btn-info" value="Submit" />
            </div>
        </form>
    </div>    
        <?php 
           
          $ids= $_GET['id'];
          $sql1 = "SELECT * FROM request WHERE id = {$ids}";
          $result = mysqli_query($con, $sql1);
          $row = mysqli_fetch_array($result);
          if(isset($_POST['btn_update'])){
            $reject_comments = $_POST['reject_comments'];
            $updateQuery = "UPDATE request 
			SET reject_comments = '".$_POST["reject_comments"]."'
			WHERE id={$ids}";
            $isUpdated = mysqli_query($con, $updateQuery);

            if (isset($_GET['id']))
            {
                $course_id=$_GET['id'];
        
                $sql="UPDATE `request` SET
                    `status`= 3 WHERE id='$course_id'";
        
                // Execute the query
                mysqli_query($con,$sql);
            }  

            if( $isUpdated){
               
        ?>
                <script>alert("DATA updat")</script>
                 <?php }
                else{ ?> 
                <script> alert ("no update")</script>
                <?php } 
    }
     
	// Check if id is set or not, if true,
	// toggle else simply go back to the page
	
    
		// Store the value from get to
		// a local variable "course_id"

		

		// SQL query that sets the status to
		// 0 to indicate deactivation.
		// $sql="UPDATE `request` SET
		// 	`status`=3 WHERE id='$course_id'";

		// // Execute the query
		// mysqli_query($con,$sql);
	

	// Go back to list page after done
    header("location:requestlist.php"); 

    //using echo function
    //     echo "<script>
    
    //     window.history.go(-1);
    // </script>";
?>






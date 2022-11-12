<?php

	// Connect to database
	$con=mysqli_connect("127.0.0.1:4306","root","","test2");

	// Check if id is set or not, if true,
	// toggle else simply go back to the page
	if (isset($_GET['id'])){

		// Store the value from get to
		// a local variable "course_id"
		$course_id=$_GET['id'];

		// SQL query that sets the status to
		// 0 to indicate deactivation.
		$sql="UPDATE `courses` SET
			`status`=0 WHERE id='$course_id'";

		// Execute the query
		mysqli_query($con,$sql);
	}

	// Go back to list page after done
    header("location:toggle.php"); 

    //using echo function
//     echo "<script>
    
//     window.history.go(-1);
// </script>";
?>

<?php
session_start();
require('../admin/include/config.php');
class User extends Dbconfig {	
    protected $hostName;
    protected $userName;
    protected $password;
	protected $dbName;
	private $userTable = 'user';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 		
			$database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;			
            $conn = new mysqli($this->hostName, $this->userName, $this->password, $this->dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}	
    public function loginStatus (){
		if(empty($_SESSION["userid"])) {
			header("Location: login.php");
		}
	}	
	public function login(){		
		$errorMessage = '';
		if(!empty($_POST["login"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {	
			$loginId = $_POST['loginId'];
			$password = $_POST['loginPass'];
			if(isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
				$password = $_COOKIE["loginPass"];
			} else {
				$password = md5($password);
			}	
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$loginId."' AND password='".$password."' AND status = 'active'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValidLogin = mysqli_num_rows($resultSet);	
			if($isValidLogin){
				if(!empty($_POST["remember"]) && $_POST["remember"] != '') {
					setcookie ("loginId", $loginId, time()+ (10 * 365 * 24 * 60 * 60));  
					setcookie ("loginPass",	$password,	time()+ (10 * 365 * 24 * 60 * 60));
				} else {
					$_COOKIE['loginId' ]='';
					$_COOKIE['loginPass'] = '';
				}
				$userDetails = mysqli_fetch_assoc($resultSet);
				$_SESSION["userid"] = $userDetails['id'];
				$_SESSION["name"] = $userDetails['first_name']." ".$userDetails['last_name'];
				header("location: index.php"); 		
			} else {		
				$errorMessage = "Invalid login!";		 
			}
		} else if(!empty($_POST["loginId"])){
			$errorMessage = "Enter Both user and password!";	
		}
		return $errorMessage; 		
	}
	public function adminLoginStatus (){
		if(empty($_SESSION["adminUserid"])) {
			header("Location: index.php");
		}
	}		
	public function adminLogin(){		
		$errorMessage = '';
		if(!empty($_POST["login"]) && $_POST["email"]!=''&& $_POST["password"]!='') {	
			$email = $_POST['email'];
			$password = $_POST['password'];
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$email."' AND password='".md5($password)."' AND status = 'active' AND type = 'administrator'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValidLogin = mysqli_num_rows($resultSet);	
			if($isValidLogin){
				$userDetails = mysqli_fetch_assoc($resultSet);
				$_SESSION["adminUserid"] = $userDetails['id'];
				$_SESSION["admin"] = $userDetails['first_name']." ".$userDetails['last_name'];
				header("location: dashboard.php"); 		
			} else {		
				$errorMessage = "Invalid login!";		 
			}
		} else if(!empty($_POST["login"])){
			$errorMessage = "Enter Both user and password!";	
		}
		return $errorMessage; 		
	}
	public function register(){		
		$message = '';
		if(!empty($_POST["register"]) && $_POST["email"] !='') {
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$_POST["email"]."'";
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$isUserExist = mysqli_num_rows($result);
			if($isUserExist) {
				$message = "User already exist with this email address.";
			} else {			
				$authtoken = $this->getAuthtoken($_POST["email"]);
				$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, password, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".md5($_POST["passwd"])."', '".$authtoken."')";
				$userSaved = mysqli_query($this->dbConnect, $insertQuery);
				if($userSaved) {				
					$link = "<a href='http://webdamn.com/demo/user-management-system/verify.php?authtoken=".$authtoken."'>Verify Email</a>";			
					$toEmail = $_POST["email"];
					$subject = "Verify email to complete registration";
					$msg = "Hi there, click on this ".$link." to verify email to complete registration.";
					$msg = wordwrap($msg,70);
					$headers = "From: info@webdamn.com";
					if(mail($toEmail, $subject, $msg, $headers)) {
						$message = "Verification email send to your email address. Please check email and verify to complete registration.";
					}
				} else {
					$message = "User register request failed.";
				}
			}
		}
		return $message;
	}	
	public function getAuthtoken($email) {
		$code = md5(889966);
		$authtoken = $code."".md5($email);
		return $authtoken;
	}	
	public function verifyRegister(){
		$verifyStatus = 0;
		if(!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {			
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE authtoken='".$_GET["authtoken"]."'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValid = mysqli_num_rows($resultSet);	
			if($isValid){
				$userDetails = mysqli_fetch_assoc($resultSet);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_GET["authtoken"]) {					
					$updateQuery = "UPDATE ".$this->userTable." SET status = 'active'
						WHERE id='".$userDetails['id']."'";
					$isUpdated = mysqli_query($this->dbConnect, $updateQuery);					
					if($isUpdated) {
						$verifyStatus = 1;
					}
				}
			}
		}
		return $verifyStatus;
	}	
	public function userDetails () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_SESSION["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}	
	public function editAccount () {
		$message = '';
		$updatePassword = '';
		if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] != $_POST["cpasswd"]) {
			$message = "Confirm passwords do not match.";
		} else if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] == $_POST["cpasswd"]) {
			$updatePassword = ", password='".md5($_POST["passwd"])."' ";
		}		
		$updateQuery = "UPDATE ".$this->userTable." 
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."' $updatePassword
			WHERE id ='".$_SESSION["userid"]."'";
		$isUpdated = mysqli_query($this->dbConnect, $updateQuery);	
		if($isUpdated) {
			$_SESSION["name"] = $_POST['firstname']." ".$_POST['lastname'];
			$message = "Account details saved.";
		}
		return $message;
	}	
	public function resetPassword(){
		$message = '';
		if($_POST['email'] == '') {
			$message = "Please enter username or email to proceed with password reset";			
		} else {
			$sqlQuery = "
				SELECT email 
				FROM ".$this->userTable." 
				WHERE email='".$_POST['email']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {			
				$user = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($user['email']);
				$link="<a href='https://www.webdamn.com/demo/user-management-system/reset_password.php?authtoken=".$authtoken."'>Reset Password</a>";				
				$toEmail = $user['email'];
				$subject = "Reset your password on examplesite.com";
				$msg = "Hi there, click on this ".$link." to reset your password.";
				$msg = wordwrap($msg,70);
				$headers = "From: info@webdamn.com";
				if(mail($toEmail, $subject, $msg, $headers)) {
					$message =  "Password reset link send. Please check your mailbox to reset password.";
				}				
			} else {
				$message = "No account exist with entered email address.";
			}
		}
		return $message;
	}
	public function savePassword(){
		$message = '';
		if($_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else if($_POST['authtoken']) {
			$sqlQuery = "
				SELECT email, authtoken 
				FROM ".$this->userTable." 
				WHERE authtoken='".$_POST['authtoken']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {				
				$userDetails = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_POST['authtoken']) {
					$sqlUpdate = "
						UPDATE ".$this->userTable." 
						SET password='".md5($_POST['password'])."'
						WHERE email='".$userDetails['email']."' AND authtoken='".$authtoken."'";	
					$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
					if($isUpdated) {
						$message = "Password saved successfully. Please <a href='login.php'>Login</a> to access account.";
					}
				} else {
					$message = "Invalid password change request.";
				}
			} else {
				$message = "Invalid password change request.";
			}	
		}
		return $message;
	}
	public function getUserList(){		
		$sqlQuery = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		$sqlQuery1 = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		$result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result1);
		
		$userData = array();	
		while( $users = mysqli_fetch_assoc($result) ) {		
			$userRows = array();
			$status = '';
			if($users['status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else if($users['status'] == 'pending') {
				$status = '<span class="label label-warning">Inactive</span>';
			} else if($users['status'] == 'deleted') {
				$status = '<span class="label label-danger">Deleted</span>';
			}
			$userRows[] = $users['id'];
			$userRows[] = ucfirst($users['first_name']." ".$users['last_name']);
			$userRows[] = $users['gender'];			
			$userRows[] = $users['email'];	
			$userRows[] = $users['mobile'];	
			$userRows[] = $users['type'];
			$userRows[] = $status;						
			$userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			$userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData
		);
		echo json_encode($output);
	}
	
	public function deleteUser(){
		if($_POST["userid"]) {
			$sqlUpdate = "
				UPDATE ".$this->userTable." SET status = 'deleted'
				WHERE id = '".$_POST["userid"]."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);		
		}
	}
	public function getUser(){
		$sqlQuery = "
			SELECT * FROM ".$this->userTable." 
			WHERE id = '".$_POST["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updateUser() {
		if($_POST['userid']) {	
			$updateQuery = "UPDATE ".$this->userTable." 
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."', status = '".$_POST["status"]."', type = '".$_POST['user_type']."'
			WHERE id ='".$_POST["userid"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
		}	
	}	
	public function saveAdminPassword(){
		$message = '';
		if($_POST['password'] && $_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else {			
			$sqlUpdate = "
				UPDATE ".$this->userTable." 
				SET password='".md5($_POST['password'])."'
				WHERE id='".$_SESSION['adminUserid']."' AND type='administrator'";	
			$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
			if($isUpdated) {
				$message = "Password saved successfully.";
			}				
		}
		return $message;
	}
	public function adminDetails () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_SESSION["adminUserid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}	
	public function addUser () {
		if($_POST["email"]) {
			$authtoken = $this->getAuthtoken($_POST['email']);
			$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["gender"]."', '".md5($_POST["password"])."', '".$_POST["mobile"]."', '".$_POST["designation"]."', '".$_POST['user_type']."', 'active', '".$authtoken."')";
			$userSaved = mysqli_query($this->dbConnect, $insertQuery);
		}
	}
	public function totalUsers ($status) {
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sqlQuery = "SELECT * FROM employees 
		WHERE manager_id ='".$_SESSION["adminUserid"]."' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}

	public function totalAssets ($status) {
		$query = '';
		$a=0;
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sqlQuery = "SELECT * FROM employees 
		WHERE manager_id ='".$_SESSION["adminUserid"]."' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		//$results = mysqli_fetch_assoc($result);
		$results1 = $result->fetch_all(MYSQLI_ASSOC);
		

		foreach($results1 as $output ) {
			//employee details collection
			$emp_id = $output["id"];
			//echo $emp_id;

			$sqlQuery = "SELECT * FROM assets 
		WHERE empid ='$emp_id' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		//$results = mysqli_fetch_assoc($result);
		$results1 = $result->fetch_all(MYSQLI_ASSOC);
		

		foreach($results1 as $output ) {
			//employee details collection
			$emp_id = $output["id"];
			//echo $emp_id;
			++$a;
		}

		}
		

		return $a;
		
			
	}

	//get employeelist
	public function getemployeeList(){		
		$sqlQuery = "SELECT * FROM employees WHERE manager_id ='".$_SESSION['adminUserid']."' ";
		//no search required
		// if(!empty($_POST["search"]["value"])){
		// 	$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';			
		// }
		// if(!empty($_POST["order"])){
		// 	$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		// } else {
		// 	$sqlQuery .= 'ORDER BY id DESC ';
		// }
		// if($_POST["length"] != -1){
		// 	$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		// }	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		// $sqlQuery1 = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		// $result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result);
		
		$userData = array();	
		while( $users = mysqli_fetch_assoc($result) ) {		
			$userRows = array();
			//no status is coming 
			// $status = '';
			// if($users['status'] == 'active')	{
			// 	$status = '<span class="label label-success">Active</span>';
			// } else if($users['status'] == 'pending') {
			// 	$status = '<span class="label label-warning">Inactive</span>';
			// } else if($users['status'] == 'deleted') {
			// 	$status = '<span class="label label-danger">Deleted</span>';
			// }
			$userRows[] = $users['id'];
			$userRows[] = $users['fullname'];			
			$userRows[] = $users['email'];	
			$userRows[] = $users['mobile_number'];	
			$userRows[] = $users['jobrole'];
			$userRows[] = $users['city'];
			// $userRows[] = $status;						
			$userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			// $userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData
		);
		echo json_encode($output);
	}

	public function getEmployee(){
		$sqlQuery = "
			SELECT * FROM employees 
			WHERE id = '".$_POST["employeeid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}


	public function updateEmployee() {
		if($_POST['employeeid']) {	
			$updateQuery = "UPDATE employees 
			SET fullname = '".$_POST["fullname"]."', email = '".$_POST["email"]."', mobile_number = '".$_POST["mobile_number"]."', jobrole = '".$_POST["jobrole"]."' , city = '".$_POST["city"]."', address = '".$_POST["address"]."'
			WHERE id ='".$_POST["employeeid"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
		}	
	}

	public function addEmployee () {
		if($_POST["email"]) {
			$authtoken = $this->getAuthtoken($_POST['email']);
			$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["gender"]."', '".md5($_POST["password"])."', '".$_POST["mobile"]."', '".$_POST["designation"]."', '".$_POST['user_type']."', 'active', '".$authtoken."')";
			$userSaved = mysqli_query($this->dbConnect, $insertQuery);
		}
	}

	//get Asset List
	public function getAssetList(){	
		
		// $query = '';
		// if($status) {
		// 	$query = "SELECT * FROM employees WHERE manager_id ='".$_SESSION['adminUserid']."' ";
		// }
		// $sqlQuery = "SELECT * FROM assets 
		// WHERE empid ='$emp_id' $query";

		$sqlQuery = "SELECT a.id, a.assettag, a.name, a.serial, a.description FROM assets a, employees e WHERE e.manager_id ='".$_SESSION['adminUserid']."' AND a.empid = e.id";
		
		//  WHERE id ='".$_SESSION['adminUserid']."' ";
		//no search required
		// if(!empty($_POST["search"]["value"])){
		// 	$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';			
		// }
		// if(!empty($_POST["order"])){
		// 	$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		// } else {
		// 	$sqlQuery .= 'ORDER BY id DESC ';
		// }
		// if($_POST["length"] != -1){
		// 	$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		// }	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		// $sqlQuery1 = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		// $result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result);
		
		$userData = array();	
		while( $users = mysqli_fetch_assoc($result) ) {		
			//echo $users;
			$userRows = array();
			//no status is coming 
			// $status = '';
			// if($users['status'] == 'active')	{
			// 	$status = '<span class="label label-success">Active</span>';
			// } else if($users['status'] == 'pending') {
			// 	$status = '<span class="label label-warning">Inactive</span>';
			// } else if($users['status'] == 'deleted') {
			// 	$status = '<span class="label label-danger">Deleted</span>';
			// }
			$userRows[] = $users['id'];
			$userRows[] = $users['assettag'];			
			$userRows[] = $users['name'];	
			$userRows[] = $users['serial'];	
			$userRows[] = $users['description'];
			// $userRows[] = $status;						
			// $userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			// $userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData
		);
		echo json_encode($output);
	}

	// request list
	public function getRequestList(){		
		$sqlQuery = "SELECT * FROM request WHERE managerid ='".$_SESSION['adminUserid']."' ";

		//   WHERE id !='".$_SESSION['adminUserid']."' ";
		// if(!empty($_POST["search"]["value"])){
		// 	$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
		// 	$sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';			
		// }
		// if(!empty($_POST["order"])){
		// 	$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		// } else {
		// 	$sqlQuery .= 'ORDER BY id DESC ';
		// }
		// if($_POST["length"] != -1){
		// 	$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		// }	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		// $sqlQuery1 = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		// $result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result);
		
		$userData = array();	
		while( $users = mysqli_fetch_assoc($result) ) {		
			$userRows = array();
			// $status = '';
			// if($users['status'] == 'active')	{
			// 	$status = '<span class="label label-success">Active</span>';
			// } else if($users['status'] == 'pending') {
			// 	$status = '<span class="label label-warning">Inactive</span>';
			// } else if($users['status'] == 'deleted') {
			// 	$status = '<span class="label label-danger">Deleted</span>';
			// }
			$userRows[] = $users['id'];
			$userRows[] = $users['status'];
			$userRows[] = $users['employee_name'];			
			$userRows[] = $users['company'];	
			$userRows[] = $users['asset_type'];	
			$userRows[] = $users['asset_details'];
			// $userRows[] = $status;						
// 			$userRows[] = if($users['id']=="1"){
// 				echo
// "<a href=deactivate.php?id=".$users['id']." class='btn red'>Deactivate</a>";
// echo
// "<a href=activate.php?id=".$users['id']." class='btn green'>Activate</a>";}
// 			else
// 				echo "Active";
		$userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			$userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData
		);
		echo json_encode($output);
	}

	public function totalRequests () {
		$query = '';
		
		$sqlQuery = "SELECT * FROM request 
		WHERE managerid ='".$_SESSION["adminUserid"]."' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}

	public function totalPendingRequests () {
		$query = '';
		
		$sqlQuery = "SELECT * FROM request 
		WHERE managerid ='".$_SESSION["adminUserid"]."' AND status NOT IN (2,3,4,5) $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}

	public function totalApprovedAssets () {
		$query = '';
		
		$sqlQuery = "SELECT * FROM request 
		WHERE managerid ='".$_SESSION["adminUserid"]."' AND status  IN (2) $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}

}
?>
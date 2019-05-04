<?php

require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;

require 'settings/database.login.php';

$message = '';
////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$isAutoAcceptRegistrationEnabled = getSetting("", "enableAutoRegistration");
$defaultGroupId = getSetting("", "newUserDefaultGroupId");
$appMode = getSetting("", "appMode");
if($appMode == "0"){
    //Debug mode
    Debugger::enable();
}
if(isset($_GET['activation_code']))
{
	//chek email in users  table - TODO
	$query = "SELECT * FROM registration_request WHERE activation_code = :user_activation_code";
	$statement = $conn->prepare($query);
	$statement->execute(array(':user_activation_code'=>	$_GET['activation_code']));
	$count = $statement->rowCount();
	
	if($count > 0){
		$result = $statement->fetchAll();
		foreach($result as $row){
			if($row['is_confirm'] == '0'){
				$uIdx = $row['indx'];
				$uName = $row['user_name'];
				$eMail = $row['email'];
				$uPass = $row['password'];
				$uStatus = "1";
				if($isAutoAcceptRegistrationEnabled == "1"){
					//1. insert to 'users' table
					try {
						$stmt = $conn->prepare("INSERT INTO users (username, email, password, status, groups) VALUES
												(:name, :email, :pass, :stt, :group)");
						$stmt->bindParam(':name', $uName);
						$stmt->bindParam(':email', $eMail);
						$stmt->bindParam(':pass', $uPass);
						$stmt->bindParam(':stt', $uStatus);
						$stmt->bindParam(':group', $defaultGroupId);
						$stmt->execute();
						$message = '<label class="text-success">Your Email Address Successfully Verified <br />You can login here - <a href="login.php">Login</a></label>';
						//2. delete from 'registration_request'
						try {
							$stmtDel = $conn->prepare("DELETE FROM registration_request WHERE indx = $uIdx");
							$stmtDel->execute();
						}catch(PDOException $e){
							$message = '<label class="text-danger">'. $e->getMessage() . '</label>';
						}
					}catch(PDOException $e){
						$message = '<label class="text-danger">'. $e->getMessage() . '</label>';
					}
				}else{
					$update_query = "UPDATE registration_request SET is_confirm = '1' WHERE email = '$eMail'";
					$statement = $conn->prepare($update_query);
					$statement->execute();
					$sub_result = $statement->fetchAll();
					if(isset($sub_result)){
						$message = '<label class="text-success">Your Email Address Successfully Verified <br />You can login here - <a href="login.php">Login</a></label>';
					}
				}
			}else{
				$message = '<label class="text-info">Your Email Address Already Verified</label>';
			}
		}
	}else{
		$message = '<label class="text-danger">Invalid Link</label>';
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register Email Verification</title>		
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->	
		<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<!--===============================================================================================-->
		<link rel="stylesheet" href="./include/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="./include/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="css/util.css">
		<link rel="stylesheet" type="text/css" href="css/main.css">
	<!--===============================================================================================-->	
	</head>
	<body>
		<div class="limiter">
			<div class="container-login100" style="background-image: url('images/bg05.jpg');">
				<div class="wrap-login100 p-b-100">
					<h1 style = "align:center;">Register Email Verification</h1>
			
					<div class="login100-form-title p-t-20 p-b-45">
						<?php if(!empty($message)): ?>
							<p style="background-color:white;"><?= $message ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	
	</body>
	
</html>
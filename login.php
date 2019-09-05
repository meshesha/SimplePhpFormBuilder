<?php

require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;
////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$appMode = getSetting("", "appMode");

if($appMode == "0"){
	//Debug mode
	Debugger::enable();
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
/*
if( isset($_SESSION['user_id']) ){
	if($_SESSION['rederect_url'] == "main_page"){
		header("Location: index.php");
	}else if($_SESSION['rederect_url'] == "form_admin"){
		header("Location: formadmin.php");
	}
}
*/
require 'settings/database.login.php';
$isShowLogin = true;
if(!empty($_POST['username']) && !empty($_POST['password'])){
	$status = "1";
	$records = $conn->prepare('SELECT id,username,password,groups FROM users WHERE status = :stt AND username = :username');
	$records->bindParam(':username', $_POST['username']);
	$records->bindParam(':stt', $status);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);

	$userId = $results['id'];
	$userPass = $results['password'];
	$groups = $results['groups'];
	$message = '';

	if($results != '' && count($results) > 0 ){
		if(password_verify($_POST['password'], $userPass)){
			$_SESSION['user_id'] = $userId;
			if( isset($_SESSION['user_id']) ){
				if($_SESSION['rederect_url'] == "main_page"){
					header("Location: index.php");
				}else if($_SESSION['rederect_url'] == "form_admin"){
					if(isset($_SESSION['form_id'])){
						$formId = $_SESSION['form_id'];
						header("Location: formadmin.php?id=$formId");
					}else{
						header("Location: formadmin.php");
					}
				}else if($_SESSION['rederect_url'] == "form"){
					if(isset($_SESSION['form_id'])){
						$formId = $_SESSION['form_id'];
						header("Location: form.php?id=$formId");
					}else{
						header("Location: form.php");
					}
				}
			}
		}else{
			$message = 'Sorry, wrong Username or password. please try again!';
		}
	} else {
		$message = 'Sorry, Username does not exist or is suspended';
	}
}


$isRegistrationEnabled = getSetting("", "enableUserRegistration");
$isPassRecoveryEnabled = getSetting("", "enableUserPasswordRecovery");

/*
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
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
			<div class="wrap-login100 p-b-30">
				<form class="login100-form validate-form" action="login.php" method="POST">
					
					<div class="login100-form-avatar">
						<img src="images/dp.png" alt="AVATAR">
					</div>

					<span class="login100-form-title p-t-20 p-b-45">
					<?php if(!empty($message)): ?>
						<p style="background-color:white;"><?= $message ?></p>
					<?php endif; ?>
					</span>
					<?php if($isShowLogin): ?>
					<div class="wrap-input100 validate-input m-b-10" data-validate = "Enter your user name">
						<input class="input100" type="text" name="username" placeholder="user name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "password">
						<input class="input100" type="password" name="password" placeholder="password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock"></i>
						</span>
					</div>

					<div class="container-login100-form-btn p-t-10">
						<input type="submit" class="login100-form-btn" value="submit" />
					</div>
						<?php if(!empty($isPassRecoveryEnabled) && $isPassRecoveryEnabled == "1"): ?>
						<div class="text-center w-full">
							<a href="forgot_password.php" class="txt1">
								Forgot Username / Password?
							</a>
						</div>
						<?php endif; ?>
						<?php if(!empty($isRegistrationEnabled) && $isRegistrationEnabled == "1"): ?>
						<div class="text-center w-full">
							<a class="txt1" href="register.php">
								Create new account
								<i class="fa fa-long-arrow-right"></i>						
							</a>
						</div>
						<?php endif; ?>
					<?php endif; ?>
				</form>
			</div>
		</div>
	</div>
<!--===============================================================================================-->	
	<script src="./include/jquery/jquery-1.12.4.min.js"></script>
	<script src="js/main.js"></script>

</body>
</html>
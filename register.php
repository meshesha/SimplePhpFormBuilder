<?php

require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if( isset($_SESSION['user_id']) ){
	if($_SESSION['rederect_url'] == "main_page"){
		header("Location: index.php");
	}else if($_SESSION['rederect_url'] == "form_admin"){
		header("Location: formadmin.php");
	}else{
        header("Location: /");
    }
}

require 'settings/database.login.php';

$message = '';

////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$isRegistrationEnabled = getSetting("", "enableUserRegistration");
if($isRegistrationEnabled == "0"){
    die("Registration not allowed!");
}
$emailSetting = getSetting("email", "");
if(empty($emailSetting)){
    die("Error getting email server setting!");
}
$appMode = getSetting("", "appMode");
if($appMode == "0"){
    //Debug mode
    Debugger::enable();
}

if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])){
	$query_users = "SELECT * FROM users WHERE username=:user_Name OR email=:user_Email";
 	$statement_users = $conn->prepare($query_users);
	$statement_users->bindParam(':user_Email', $_POST['email']);
	$statement_users->bindParam(':user_Name',$_POST['username']);
	$statement_users->execute();
	$count_users = $statement_users->rowCount();

	if($count_users > 0){
		$message = '<label class="text-danger">Email or user name Already Exits in users</label>';
	}else{
		$query_request = "SELECT * FROM registration_request WHERE email=:user_email OR user_name=:userName";
		$statement_request = $conn->prepare($query_request);
		$statement_request->bindParam(':user_email', $_POST['email']);
		$statement_request->bindParam(':userName',$_POST['username']);
		$statement_users->execute();
		$count_request = $statement_request->rowCount();
		if($count_request > 0){
			$message = '<label class="text-danger">Email or user name Already Exits in registration request</label>';
		}else{
			// Enter the new user in the database
			$user_activation_code = md5(rand());
			$sql = "INSERT INTO registration_request (user_name,email, password,activation_code) VALUES (:user_name, :email, :password, :activation)";
			$stmt = $conn->prepare($sql);
			$encryptPass =  password_hash($_POST['password'], PASSWORD_BCRYPT);
			$stmt->bindParam(':user_name', $_POST['username']);
			$stmt->bindParam(':email', $_POST['email']);
			$stmt->bindParam(':password',$encryptPass);
			$stmt->bindParam(':activation', $user_activation_code);

			if( $stmt->execute() ){
				//$message = 'Successfully created new user';
				$base_url = getBaseUrl();
				$mail_body = "<p>Hi ".$_POST['username'].",</p>
				<p>Thanks for Registration. Your password is '".$_POST['password']."', This password will work only after your email verification.</p>
				<p>Please Open this link to verified your email address - ".$base_url."email_verification.php?activation_code=".$user_activation_code."
				<p>Best Regards,";
				$to_email = $_POST["email"];
				$from_email = $emailSetting["from_email"];//"info@local.test";
				$subject = $emailSetting["reset_pass_mail_subject"];//"Email Verification";
				require 'settings/mail/phpmailer/class/class.phpmailer.php';
				$mail = new PHPMailer(true);
				try {
					$mail->IsSMTP();        //Sets Mailer to send message using SMTP
					$mail->Host = $emailSetting["SMTP_host"]; //'localhost';  //Sets the SMTP hosts of your Email hosting, this for Godaddy
					$mail->Port = $emailSetting["SMTP_port"]; //'25';        //Sets the default SMTP server port
					$mail->SMTPAuth = ($emailSetting["SMTP_Auth"]=="1")?true:false; //false; //Sets SMTP authentication. Utilizes the Username and Password variables
					$mail->Username = $emailSetting["SMTP_Username"]; //Sets SMTP username
					$mail->Password = $emailSetting["SMTP_Password"]; //Sets SMTP password
					$mail->SMTPSecure = $emailSetting["SMTP_Secure"]; //Sets connection prefix. Options are "", "ssl" or "tls"
					$mail->From = $from_email;//'info@webslesson.info';   //Sets the From email address for the message
					$mail->FromName = $emailSetting["from_name"];//'localhost';     //Sets the From name of the message
					$mail->AddAddress($to_email, $_POST['username']);  //Adds a "To" address   
					$mail->WordWrap = 400;       //Sets word wrapping on the body of the message to a given number of characters
					$mail->IsHTML(true);       //Sets message type to HTML    
					$mail->Subject = $subject;   //Sets the Subject of the message
					$mail->Body = $mail_body;       //An HTML or plain text message body
					if($mail->Send()){        //Send an Email. Return true on success or false on error
						$message = '<label class="text-success">Register Done, Please check your mail.</label>';
					}else{
						$message = "<label class='text-danger'>Email sending failed: " . $mail->ErrorInfo ." (phpmailer error)</label>";
						$delRqstStt = deleteUserRequest($conn, $user_activation_code);
						//$message .= " ($delRqstStt)"; 
					}
				}catch(phpmailerException $e) {
					$message = "<label class='text-danger'>Email sending failed: ".$e->errorMessage()." (phpmailer error)</label>";
					$delRqstStt = deleteUserRequest($conn, $user_activation_code);
					//$message .= " ($delRqstStt)";
				}catch(Exception $e) {
					$message = "<label class='text-danger'>Email sending failed: " . $e->getMessage() ." (general error)</label>";
					$delRqstStt = deleteUserRequest($conn, $user_activation_code);
					//$message .= " ($delRqstStt)";
				}
				/*
				if (mail($to_email, $subject, $mail_body, $headers)) {
					$message = '<label class="text-success">Register Done, Please check your mail.</label>';
				} else {
					$message = '<label class="text-danger">Email sending failed...</label>';
				}
				*/
			}else{
				$message = '<label class="text-danger">Sorry there must have been an issue creating your account</label>';
			}
		}
	}
}
function deleteUserRequest($conn, $user_activation_code){
	$stt = "";
	try {
		$stmtDel = $conn->prepare("DELETE FROM registration_request WHERE activation_code = '$user_activation_code'");
		$stmtDel->execute();
		$stt = "success";
	}catch(PDOException $e){
		$stt =  $e->getMessage();
	}
	return $stt;
}
function getBaseUrl(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    
  $host  = $_SERVER['HTTP_HOST'];
  $host_upper = strtoupper($host);
  $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    return $protocol . "://" . $host . $path . "/";
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
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
        <?php if(!empty($message)): ?>
            <p class=" p-t-20 p-b-20" style = "text-align:center;"><?= $message ?></p>
        <?php endif; ?>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg05.jpg');">
			<div class="wrap-login100 p-b-100">
				<h1>Register</h1>
				<span>or <a href="login.php">login here</a></span>
				<br>
				<form class="login100-form validate-form" action="register.php" method="POST">
					<span class="login100-form-title p-t-20 p-b-20">
					</span>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "Enter your user name">
						<input class="input100" type="text" name="username" placeholder="user name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "Enter your email (format: xxx@xxx.xxx)">
						<input class="input100" type="text" name="email" placeholder="email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "password">
						<input class="input100 main_password" type="password" name="password" placeholder="password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "confirm password - Passwords Don't Match">
						<input class="input100" type="password" name="confirm_password" placeholder="confirm password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock"></i>
						</span>
					</div>

					<div class="container-login100-form-btn p-t-10">
						<input type="submit" class="login100-form-btn" value="submit" />
					</div>
					
				</form>
			</div>
		</div>
	</div>

	
<!--===============================================================================================-->	
	<script src="./include/jquery/jquery-1.12.4.min.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>
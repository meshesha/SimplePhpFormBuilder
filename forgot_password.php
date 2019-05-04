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
$isValidLink = false;

////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$isPassRecoveryEnabled = getSetting("", "enableUserPasswordRecovery");
if($isPassRecoveryEnabled == "0"){
    die("Password Recovery not allowed!");
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

if(!empty($_POST['email'])){
    $isValidLink = false;
	$query = "SELECT * FROM users WHERE email = :user_email";
 	$statement = $conn->prepare($query);
	$statement->execute(array(':user_email' => $_POST['email']));
	$count = $statement->rowCount();
	if($count > 0){
        $result = $statement->fetchAll();
        $userName = "";
		foreach($result as $row){
            $userName = $row['username'];
        }
        $forgot_verify_code = md5(rand());
        $eMail = $_POST['email'];
        $update_query = "UPDATE users SET forgot_verify = '$forgot_verify_code' WHERE email = '$eMail'";
        $statement = $conn->prepare($update_query);
        $statement->execute();
        $sub_result = $statement->fetchAll();
        if(isset($sub_result)){
            $base_url = getBaseUrl();
            $mail_body = "<p>Hi $userName,</p>
            <p>Please Open this link to reset your password - ".$base_url."password_reset.php?verify_code=".$forgot_verify_code."
            <p>Best Regards,";
            $to_email = $eMail;
            $from_email = $emailSetting["from_email"];//"info@local.test";
            $subject = $emailSetting["reset_pass_mail_subject"];//"Reset password";
            require 'settings/mail/phpmailer/class/class.phpmailer.php';
				$mail = new PHPMailer(true);
				try {
                    $mail->IsSMTP();  //Sets Mailer to send message using SMTP
                    $mail->Host = $emailSetting["SMTP_host"]; //'localhost';  //Sets the SMTP hosts of your Email hosting, this for Godaddy
                    $mail->Port = $emailSetting["SMTP_port"]; //'25';        //Sets the default SMTP server port
                    $mail->SMTPAuth = ($emailSetting["SMTP_Auth"]=="1")?true:false; //false; //Sets SMTP authentication. Utilizes the Username and Password variables
                    $mail->Username = $emailSetting["SMTP_Username"]; //Sets SMTP username
                    $mail->Password = $emailSetting["SMTP_Password"]; //Sets SMTP password
                    $mail->SMTPSecure = $emailSetting["SMTP_Secure"]; //Sets connection prefix. Options are "", "ssl" or "tls"
                    $mail->From = $from_email; //'info@webslesson.info';   //Sets the From email address for the message
                    $mail->FromName = $emailSetting["from_name"];//'localhost';     //Sets the From name of the message
                    $mail->AddAddress($to_email, $userName);  //Adds a "To" address   
                    $mail->WordWrap = 200;       //Sets word wrapping on the body of the message to a given number of characters
                    $mail->IsHTML(true);       //Sets message type to HTML    
                    $mail->Subject = $subject;   //Sets the Subject of the message
                    $mail->Body = $mail_body;       //An HTML or plain text message body
					if($mail->Send()){        //Send an Email. Return true on success or false on error
						$message = '<label class="text-success">Register Done, Please check your mail.</label>';
					}else{
						$message = "<label class='text-danger'>Email sending failed: " . $mail->ErrorInfo ." (phpmailer error)</label>";
					}
				}catch(phpmailerException $e) {
					$message = "<label class='text-danger'>Email sending failed: ".$e->errorMessage()." (phpmailer error)</label>";
				}catch(Exception $e) {
					$message = "<label class='text-danger'>Email sending failed: " . $e->getMessage() ." (general error)</label>";
				}
        }else{
            $message = '<label class="text-danger">Sorry there must have been an issue reading data from database.</label>';
        }
	}else{
		$message = '<label class="text-danger">Email Not Exits</label>';
	}
}else{
    $isValidLink = true;
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
	<title>Forgot password</title>
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

    <?php if(!$isValidLink){ ?>
    <div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg05.jpg');">
            <div class="wrap-login100  p-b-100">
                <h1>Forgot password</h1>
                <?php if(!empty($message)): ?>
                    <p class=" p-t-20 p-b-20" style = "text-align:center;background-color:white;"><?= $message ?></p>
                <?php endif; ?>
             </div>
        </div>
    </div>
    <?php }else{ ?>

    <div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg05.jpg');">
			<div class="wrap-login100  p-b-100">
				<h1>Forgot password</h1>
                <form class="login100-form validate-form"  action="forgot_password.php"  method="POST">
					<span class="login100-form-title p-t-20 p-b-45"></span>

					<div class="wrap-input100 validate-input m-b-10" data-validate = "Enter your email (format: xxx@xxx.xxx)">
						<input class="input100" type="text" name="email" placeholder="email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user"></i>
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

        <?php }?>

</body>
</html>
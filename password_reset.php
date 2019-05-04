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

require 'settings/database.login.php';

$message = "";
$isValidLink = false;
$verifyCod = "";
if(isset($_GET['verify_code']) && $_GET['verify_code'] != ""){
	$query = "SELECT * FROM users WHERE forgot_verify = :verified_code";
	$statement = $conn->prepare($query);
	$statement->execute(array(':verified_code'=>$_GET['verify_code']));
	$count = $statement->rowCount();
	if($count > 0){
        $isValidLink = true;
        $verifyCod = $_GET['verify_code'];
		$message = '<label class="text-success">valid Link</label>';
	}else{
		$message = '<label class="text-danger">Invalid Link</label>';
	}
}else{
    $message = '<label class="text-danger">Invalid Link</label>';
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Reset Password</title>		
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
                        <h1>Reset Password</h1>
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
                        <h1>Reset Password</h1>
                        <form  class="login100-form validate-form" action="rest_password_process.php" method="POST">

                            <input type="hidden" name="verifycode" value="<?php echo $verifyCod; ?>" />

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
            
        <?php }?>
	</body>
	
</html>
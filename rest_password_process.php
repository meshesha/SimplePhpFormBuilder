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

$message = '';

if(!empty($_POST['verifycode']) && !empty($_POST['password'])){
	$query = "SELECT * FROM users WHERE forgot_verify = :verified_code";
 	$statement = $conn->prepare($query);
	$statement->execute(array(':verified_code' => $_POST['verifycode']));
	$count = $statement->rowCount();
	if($count > 0){
        
        $result = $statement->fetchAll();
        $userId = "";
		foreach($result as $row){
            $userId = $row['id'];
        }
        $new_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$sql = "UPDATE users SET forgot_verify = '',password='$new_pass' WHERE id = $userId";
		$stmt = $conn->prepare($sql);
		if( $stmt->execute() ){
			//$message = 'Successfully created new user';
            $message = '<label class="text-success">Password reset successful</label>
                        <div class="container-login100-form-btn p-t-10">
			            <a class="login100-form-btn" href="login.php">login</a> </div>';
		}else{
			$message = '<label class="text-danger">Sorry there must have been an issue resetting your password</label>';
		}
	}else{
		$message = '<label class="text-danger">Invalid verify code</label>';
	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Reset Password</title>
	<meta charset="UTF-8">
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
            <div class="wrap-login100  p-b-100">
                <h1>Reset Password</h1>
                <?php if(!empty($message)): ?>
                    <p class=" p-t-20 p-b-20" style = "text-align:center;background-color:white;"><?= $message ?></p>
                <?php endif; ?>
             </div>
        </div>
    </div>
</body>
</html>
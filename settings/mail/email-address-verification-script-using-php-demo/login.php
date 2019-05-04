<?php
//login.php

include('database_connection.php');

if(isset($_SESSION['user_id']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{
	$query = "
	SELECT * FROM register_user 
		WHERE user_email = :user_email
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
				'user_email'	=>	$_POST["user_email"]
			)
	);
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if($row['user_email_status'] == 'verified')
			{
				if(password_verify($_POST["user_password"], $row["user_password"]))
				//if($row["user_password"] == $_POST["user_password"])
				{
					$_SESSION['user_id'] = $row['register_user_id'];
					header("location:index.php");
				}
				else
				{
					$message = "<label>Wrong Password</label>";
				}
			}
			else
			{
				$message = "<label class='text-danger'>Please First Verify, your email address</label>";
			}
		}
	}
	else
	{
		$message = "<label class='text-danger'>Wrong Email Address</label>";
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>PHP Register Login Script with Email Verification</title>		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container" style="width:100%; max-width:600px">
			<h2 align="center">PHP Register Login Script with Email Verification</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Login</h4></div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="email" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
					<p align="right"><a href="register.php">Register</a></p>
				</div>
			</div>
		</div>
	</body>
</html>
<?php

include_once 'common.php';

if(  isset($_POST["username"]) && isset($_POST["pwd"])){
	$username = $_POST["username"];
	$pwd = $_POST["pwd"];

	$login = IoC::resolve("Login");
	$login->setUsername($username);
	$login->setPassword($pwd);

	if($login->createLogin()){
		header('Location: insert.php');
	} else {
		$errors = $login->errors;
	}
}

$user->is_logged_in();



?>
<!DOCTYPE html>
<html>
<head>
	<title>NKN - Login</title>	
	<link rel="stylesheet" href="style/style.css">
</head>
<div class="main">
	<h3>Noch kein Name</h3>
	<form class="login" action="login.php" method="post">
		<p><b>Username:</b><br><input type="text" class="text" id="username" name="username"></p>
		<p><b>Password:</b><br><input type="password" class="text" id="pwd" name="pwd"></p>
		<p><input type="submit" name="submitted" class="submit" value="Login"></p>
	</form>
	<p class="cta"><a href="">Back to Website</a> &nbsp; | &nbsp; <a href="resetpassword.php">Forgot your password?</a></p>
	<div class="reqs">
	<?php if(isset($errors)): ?>
		<?php foreach($errors as $error): ?>
			<?php echo $error . '<br>'; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</div>
</div>
</html>
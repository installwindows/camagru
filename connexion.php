<?php
include 'database.php';
session_start();
$error_message = "";
$username = "";
$password = "";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = $_POST["username"];
	$password = $_POST["password"];
	if (check_user($_POST["username"], $_POST["password"]))
	{
		$_SESSION['user'] = $_POST["username"];
		header("Location: index.php");
		die();
	}
	else
	{
		$error_message = "Nom d'utilisateur et/ou mot de passe invalide.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Connexion</title>
</head>
<body>
<h2>Connexion</h2>
<form method="POST" action="connexion.php">
	Username: <input type="text" name="username" value="<?php echo $username; ?>"><br>
	Password: <input type="password" name="password" value="<?php echo $password; ?>"><br>
	<input type="submit" name="submit"><br>
	<?php echo $error_message; ?>
</form>
</body>
</html>

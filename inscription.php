<?php
include 'database.php';
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
	$email = htmlspecialchars($_POST['email']);
	$username = htmlspecialchars($_POST['username']);
	$password = hash("whirlpool", $_POST['password']);
	if (user_exist($email, $username))
	{

	}
	else
	{
		try {
			$pdo = get_database_connection();
			$query = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
			$query->execute(array(
				"email"		=> $email,
				"username"	=> $username,
				"password"	=> $password
			));
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Inscription</title>
</head>
<body>
<h2>Inscription</h2>
<form method="POST" action="inscription.php">
	Courriel: <input type="text" name="email"><br>
	Username: <input type="text" name="username"><br>
	Password: <input type="password" name="password"><br>
	<input type="submit" name="submit" value="Soumettre"><br>
</form>
</body>
</html>

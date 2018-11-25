<?php
include 'database.php';
include 'validate.php';
session_start();
if (isset($_SESSION['user']))
{
	$user = get_user_by_username($_SESSION['user']);
}
else
{
	header("Location: connexion.php");
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION['user']))
{
	if (isset($_POST['update_email']))
	{
		$email = strtolower($_POST['email']);
		if (!validate_email($email))
		{
			$email_message = "Ceci n'est pas une syntax de courriel acceptable.";
		}
		else if (empty(get_user_by_email($email)))
		{
			send_task($user['id'], "change_email", $email);
			$email_message = "Veuillez confirmez le nouveau courriel en suivant le lien envoyé à ce dernier.";
		}
		else
		{
			$email_message = "Courriel déjà utilisé.";
		}
	}
	if (isset($_POST['update_username']))
	{
	}
	if (isset($_POST['update_password']))
	{
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Compte</title>
</head>
<body>
<?php echo $user['email']; ?><br>
<?php echo $user['username']; ?><br>
<hr>
<form method="POST" action="compte.php">
	Changer courriel: <input type="text" name="email">
	<input type="submit" name="update_email" value="Confirmer">
</form>
<div><?php echo $email_message; ?></div>
<hr>
<form method="POST" action="compte.php">
	Changer nom d'utilisateur: <input type="text" name="username">
	<input type="submit" name="update_username" value="Confirmer">
</form>
<div><?php echo $username_message; ?></div>
<hr>
<form method="POST" action="compte.php">
	Changer mot de passe: <input type="password" name="password">
	<input type="submit" name="update_password" value="Confirmer">
</form>
<div><?php echo $password_message; ?></div>
</body>
</html>

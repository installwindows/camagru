<?php
include 'database.php';
include 'validate.php';
session_start();
if (isset($_SESSION["user_id"]))
	header("Location: compte.php");
$err_msg = "";
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (isset($_POST['email']))
	{
		$email = strtolower($_POST['email']);
		if (validate_email($email))
		{
			if ($user_id = get_user_by_email($email))
			{
				echo "Le <abbr title='Département des Mots de Passe Perdus'>DMPP</abbr> envera le résultat de ses recherches sous peu.";
				send_task($user_id, "password_lost");
			}
			else
			{
				$err_msg = "Es-tu sûr qu'il s'agit bien de ton adresse courriel?";
			}
		}
		else
		{
			$err_msg = "Cette adresse courriel est REFUSÉE.";
		}
	}
	else if (isset($_POST['id']))
	{
		$id = $_POST['id'];
		$password = $_POST['password'];
		if (!validate_password($password))
			$err_msg = "Bad password.";
		else if ($user_id = get_email_task($id))//TODO
		{
			change_password($user_id, hash('whirlpool', $password));
			echo "Password changed!";
			remove_task($id);//TODO
			header("refresh:3; url=connexion.php");
			die();
		}
		else
			$err_msg =  "SCRAM!!!";
	}
}
if (isset($_GET['id']))
{
	//TODO
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Mot de passe oublié</title>
</head>
<body>
<p>Formulaire pour rejoindre le département des mots de passe perdus.<p>
<form method="POST" action="oublie.php">
	<?php echo "$err_msg<br>"; ?>
	Adresse courriel: <input type="text" name="email"><br>
	Détails concernant la disparition: <textarea></textarea><br>
	<input type="submit" value="Envoyer">
</form>
</body>
</html>

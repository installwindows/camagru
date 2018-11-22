<?php
session_start();
include 'database.php';
include 'validate.php';
$email = ""; $email_error = false; $email_error_message = "";
$username = ""; $username_error = false; $username_error_message = "";
$password = ""; $password_error = false; $password_error_message = "";
$label_class = "";
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
	$email = $_POST['email'];
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (!validate_email($email))
	{
		$email_error = true;
		$email_error_message = "Le courriel ne respecte pas la syntax du RFC 822.";
	}
	if (!validate_username($username))
	{
		$username_error = true;
		$username_error_message = "Le nom d'utilisateur ne respecte pas la syntax arbitraire de 3 à 32 caractères de long composés de symbols alphanumérique.";
	}
	if (!validate_password($password))
	{
		$password_error = true;
		$password_error_message = "Le mot de passe ne doit pas dépasser 255 caractères.";
	}
	if (!empty(get_user_by_email($email)))
	{
		$email_error = true;
		$email_error_message = "Le courriel est déjà utilisé.";
	}
	if (!empty(get_user_by_username($username)))
	{
		$username_error = true;
		$username_error_message = "Le nom d'utilisateur est déjà utilisé.";
	}
	if ($email_error || $username_error || $password_error)
	{
		echo "<h2>Input error</h2>";
		$label_class = "label_error";
	}
	else
	{
		if (create_user($email, $username, $password))
		{
			//send_verification_email($email, $username);
			$message = "Vérifiez votre courriel en utilisant ce lien: ";
			$id = hash('whirlpool', time() . $email . $username . $password . "camagru");
			$verification_url = (empty($_SERVER['HTTPS']) ? "http://" : "https://") . $_SERVER['HTTP_HOST'] . "/validation_courriel.php?id=" . $id;
			$message .= "<a href='$verification_url'>$verification_url</a>";
			$headers = "Content-Type: text/html; charset=UTF-8\r\n";	
			mail($email, "Vérification compte Camagru", $message, $headers);

			insert_email_id($email, $id);

			$_SESSION["user"] = $username;
			header("Location: index.php");
			die();
		}
		else
		{
			echo "<h2>SOMETHING TERRIBLY WRONG HAPPENED</h2>";
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
	<link rel="stylesheet" href="global.css">
</head>
<body>
<h2>Inscription</h2>
<form method="POST" action="inscription.php">
<label for="email" class="<?php echo $email_error ? $label_class : ""; ?>">Courriel</label>: <input type="text" name="email" id="email" value="<?php echo $email; ?>"> <?php echo $email_error_message; ?><br>
	<label for="username" class="<?php echo $username_error ? $label_class : ""; ?>">Username</label>: <input type="text" name="username" id="username" value="<?php echo $username; ?>"> <?php echo $username_error_message; ?><br>
	<label for="password" class="<?php echo $password_error ? $label_class : ""; ?>">Password</label>: <input type="password" name="password" id="password" value="<?php echo $password; ?>"> <?php echo $password_error_message; ?><br>
	<input type="submit" name="submit" value="Soumettre"><br>
</form>
</body>
</html>

<?php
session_start();
if (isset($_SESSION['user_id']))
	header("Location: compte.php");
include 'database.php';
include 'validate.php';
$email = ""; $email_error = false; $email_error_message = "";
$username = ""; $username_error = false; $username_error_message = "";
$password = ""; $password_error = false; $password_error_message = "";
$label_class = "";
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
	$email = strtolower($_POST['email']);
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (!validate_email($email))
	{
		$email_error = true;
		$email_error_message = "Le courriel ne respecte pas la syntaxe du RFC 822.";
	}
	if (!validate_username($username))
	{
		$username_error = true;
		$username_error_message = "Le nom d'utilisateur ne respecte pas la syntaxe arbitraire de 3 à 32 caractères de longs composés de symboles alphanumériques.";
	}
	if (!validate_password($password))
	{
		$password_error = true;
		$password_error_message = "Le mot de passe doit contenir au moins un caractère et ne doit pas dépasser 255 caractères.";
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
		$label_class = "label_error";
	}
	else
	{
		if ($user_id = create_user($email, $username, $password))
		{
			send_task($user_id, "inscription_email");
			header("Refresh:3; url=index.php");
			echo "Veuillez activer votre compte en suivant le lien envoyé à celui-ci.";
			die();
		}
		else
		{
			echo "<h2>SOMETHING TERRIBLY WRONG HAPPENED</h2>";
		}
	}
}
$page_title = "Inscription à Camagru!";
$page_head = "<link rel='stylesheet' href='index.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
<h2>Inscription</h2>
<form method="POST" action="inscription.php">
<label for="email" class="<?php echo $email_error ? $label_class : ""; ?>">Courriel</label>: <input type="text" name="email" id="email" value="<?php echo $email; ?>"> <?php echo $email_error_message; ?><br>
	<label for="username" class="<?php echo $username_error ? $label_class : ""; ?>">Username</label>: <input type="text" name="username" id="username" value="<?php echo $username; ?>"> <?php echo $username_error_message; ?><br>
	<label for="password" class="<?php echo $password_error ? $label_class : ""; ?>">Password</label>: <input type="password" name="password" id="password" value="<?php echo $password; ?>"> <?php echo $password_error_message; ?><br>
	<input type="submit" name="submit" value="Soumettre"><br>
</form>
</div>
<?php include 'footer.php'; ?>
</div>

<?php
include 'database.php';
session_start();
if (isset($_SESSION['user_id']))
	header("Location: compte.php");
$error_message = "";
$username = "";
$password = "";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = $_POST["username"];
	$password = $_POST["password"];
	if ($user = authenticate_user($_POST["username"], $_POST["password"]))
	{
		if ($user['email_verified'])
		{
			$_SESSION['user_id'] = $user['id'];
			header("Location: index.php");
			die();
		}
		else
		{
			$error_message = "Adresse courriel non-validée.";
		}
	}
	else
	{
		$error_message = "Nom d'utilisateur et/ou mot de passe invalide.";
	}
}
$page_title = "Connexion";
$page_head = "<link rel='stylesheet' href='index.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
<?php include 'header.php'; ?>
<h2>Connexion</h2>
<form method="POST" action="connexion.php">
	Nom d'utilisateur: <input type="text" name="username" value="<?php echo $username; ?>"><br>
	Mot de passe: <input type="password" name="password" value="<?php echo $password; ?>"><br>
	<?php if (!empty($error_message)) { ?>
		<a href="oublie.php">Mot de passe oublié?</a><br>
	<?php } ?>
	<input type="submit" name="submit"><br>
	<?php echo $error_message; ?>
</form>
</div>
<?php include 'footer.php'; ?>
</div>

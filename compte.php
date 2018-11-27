<?php
include 'database.php';
include 'validate.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION['user_id']))
{
	$email_message = "";
	$username_message = "";
	$password_message = "";
	$user = get_user_by_id($_SESSION['user_id']);
	if (isset($_POST['update_email']))
	{
		$email = strtolower($_POST['email']);
		if (!validate_email($email))
		{
			$email_message = "Ceci n'est pas une syntaxe de courriel acceptable.";
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
		$username = $_POST['username'];
		if (!validate_username($username))
			$username_message = "Souvenez-vous, uniquement des lettres et chiffres du genre « z », « y », « z », « z » ou encore « 4 », « 1 » ou bien « 9 ». MINIMUM de 3!";
		else if (empty(get_user_by_username($username)))
		{
			change_username($user['id'], $username);
			$username_message = "Nom d'utilisateur modifié avec grand succès!";
		}
		else
			$username_message = "Nom d'utilisateur déjà utilisé";
	}
	if (isset($_POST['update_password']))
	{
		$password = $_POST['password'];
		if (!validate_password($password))
			$username_message = "TOO BIG!.";
		else
		{
			change_password($user['id'], hash('whirlpool', $password));
			$username_message = "Huge success!";
		}
	}
}
if (isset($_SESSION['user_id']))
{
	$user = get_user_by_id($_SESSION['user_id']);
}
else
{
	header("Location: connexion.php");
}
$page_title = "Gestion du compte";
?>
<?php include 'head.php'; ?>
<?php include 'header.php'; ?>
<div>
Adress courriel: <?= $user['email']; ?><br>
Nom d'utilisateur: <?= $user['username']; ?><br>
</div>
<hr>
<form method="POST" action="compte.php">
	Changer courriel: <input type="text" name="email">
	<input type="submit" name="update_email" value="Confirmer">
</form>
<div><?php echo $email_message; ?></div>
<form method="POST" action="compte.php">
	Changer nom d'utilisateur: <input type="text" name="username">
	<input type="submit" name="update_username" value="Confirmer">
</form>
<div><?php echo $username_message; ?></div>
<form method="POST" action="compte.php">
	Changer mot de passe: <input type="password" name="password">
	<input type="submit" name="update_password" value="Confirmer">
</form>
<div><?php echo $password_message; ?></div>
<?php include 'footer.php'; ?>

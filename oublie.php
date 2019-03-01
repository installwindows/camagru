<?php
include 'database.php';
include 'validate.php';
session_start();
if (isset($_SESSION["user_id"]))
{
	header("Location: compte.php");
	die();
}
$err_msg = "";
$password_error = "";
$was_and_error_so_here_is_the_id = "";
$success_message = "";
$fatal_error = "";
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (isset($_POST['email']))
	{
		$email = strtolower($_POST['email']);
		if (validate_email($email))
		{
			if ($user = get_user_by_email($email))
			{
				send_task($user['id'], "password_lost");
				header("refresh:6; url=connexion.php");
				$success_message =  "Le <abbr title='Département des Mots de Passe Perdus'>DMPP</abbr> enverra le résultat de ses recherches sous peu à l'adresse indiquée.<br><a href='connection.php'>Go back!</a>";
				//die();
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
		{
			$password_error = "Bad password.";
			$was_and_error_so_here_is_the_id = $_POST['id'];
		}
		else if ($task = get_email_task($id))
		{
			change_password($task['user_id'], hash('whirlpool', $password));
			remove_task($id);
			header("refresh:3; url=connexion.php");
			$success_message =  "Password changed!";
			//die();
		}
		else
		{
			$fatal_error =  "ID invalide! Resuiver le lien envoyé à votre email ou refaite le processus d'oubliation de mot de passe.";
			//die();
		}
	}
}
$page_title = "Oubliation de mot de passe";
$page_head = "<link rel='stylesheet' href='index.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
<?php if (empty($success_message)) { ?>
	<?php if (isset($_GET['id']) || !empty($password_error))
	{
		$id = htmlspecialchars($_GET['id']);?>
		<form method="POST" action="oublie.php">
			<?php echo "$password_error<br>"; ?>
			Entrez le nouveau mot de passe: <input type="password" name="password"><br>
			<input type="hidden" name="id" value="<?php echo empty($id) ? $was_and_error_so_here_is_the_id : $id; ?>">
			<input type="submit" value="Confirmer">
		</form>
	<?php } else { ?>
	<p>Formulaire pour rejoindre le département des mots de passe perdus.<p>
	<form method="POST" action="oublie.php">
		<?php echo "$err_msg<br>"; ?>
		Adresse courriel: <input type="text" name="email"><br>
		Détails concernant la disparition: <textarea></textarea><br>
		<input type="submit" value="Envoyer">
	</form>
	<?php }
} else if (!empty($success_message)) { ?>
	<div class="success"><?= $success_message ?></div>
<?php } else { ?>
	<div class="error"><?= $fatal_error ?></div>
<?php } ?>
</div>
<?php
include 'footer.php'
?>

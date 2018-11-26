<?php
include 'database.php';
session_start();
$welcome_message = "";
if (isset($_SESSION["user_id"]))
{
	$user = get_user_by_id($_SESSION["user_id"]);
	$welcome_message = "Bienvenue {$user['username']}.";
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Camagru</title>
</head>
<body>
<a href="connexion.php">Connexion</a> | <a href="inscription.php">Inscription</a> | <a href="deconnexion.php">Déconnexion</a> | <a href="compte.php">Compte</a>
<hr>
<h2>Camagru!</h2>
<h3><?php echo "$welcome_message"; ?></h3>
</body>
</html>

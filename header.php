<body>
<div class="header">
	<a href="index.php">Camagru</a> |
	<?php if (empty($_SESSION['user_id'])) { ?>
		<a href="inscription.php">Inscription</a> |
		<a href="connexion.php">Connexion</a> |
	<?php } else { ?>
		<a href="compte.php">Compte</a> |
		<a href="deconnexion.php">Déconnexion</a>
	<?php } ?>
</div>
<hr>

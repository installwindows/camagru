<?php
include 'database.php';
session_start();
$welcome_message = "";
if (isset($_SESSION["user_id"]))
{
	$user = get_user_by_id($_SESSION["user_id"]);
}
$page_title = "Camagru: Photo montage rocambolesque";
$page_head = "<link rel='stylesheet' href='index.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
<h1>Camagru!</h1>
<?php if (isset($_SESSION['user_id'])) { ?>
	<p>Vous, <b><?= "{$user['username']}" ?></b>... comment allez-vous en ce jour-ci? Observez avec des yeux sans nuages de haine la <a href="/galerie.php">galerie</a>. L'inspiration divine pénétrera obligatoirement vos binoculaires et ainsi vous ferez le pèlerinage d'un clic vers <a href="/montage.php">l'atelier</a> pour créer votre propre vision!</p>
<?php } else { ?>
	<p>Qu'est-ce que ce Camagru? Acronyme de &laquo; <i>Cliché augmenté manuellement avec goût rêvé ultérieurement</i> &raquo;, Camagru est en constant devenir. Par l'artistique-té de nos Camagruistes, l'horizon humain n'a jamais été aussi large.</p>
	<p>Né du temps de père grand Comogro dit &laquo; <i>L'étoile de la toile</i> &raquo;, surnom dû à ses oeuvres éblouissantes causant presque la cécité, Camagru emploie le même procédé de fusion-imagique jadis utilisé par le vénérable Comogro. L'oeil guidé par un esprit lumineux saura créer des formes dignes de présentation dans notre rutilante <a href="/galerie.php">galerie</a> léguée par nos ancêtres.</p>
	<p>Trêve de blablata et dédiez les quarante-deux secondes nécessaires pour vous <a href="/inscription.php">inscrire</a> et laisser votre âme d'artiste en devenir vous guider.</p>
<?php } ?>
</div>
<?php include 'footer.php'; ?>
</div>

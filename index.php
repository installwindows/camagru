<?php
include 'database.php';
session_start();
$welcome_message = "";
if (isset($_SESSION["user_id"]))
{
	$user = get_user_by_id($_SESSION["user_id"]);
	$welcome_message = "Bienvenue {$user['username']}.";
}
$page_title = "Camagru: Photo montage rocambolesque";
?>
<?php include 'head.php'; ?>
<?php include 'header.php'; ?>
<h1>Camagru!</h1>
<h2><?= "$welcome_message"; ?></h2>
<p>Qu'est-ce que ce Camagru? Acronyme de &laquo; <i>Cliché augmenté manuellement avec goût rêvé ultérieurement</i> &raquo;, Camagru est en constant devenir. Par l'artistique-té de nos Camagruistes, l'horizon humain n'a jamais été aussi large.</p>
<p>Né du temps de père grand Comogro dit &laquo; <i>L'étoile de la toile</i> &raquo;, surnom dû à ses oeuvres éblouissantes causant presque la cécité, Camagru emploie le même procédé de fusion-imagique jadis utilisé par le vénérable Comogro. L'oeil guidé par un esprit lumineux saura créer des formes dignes de présentation dans notre rutilante galerie léguée par nos ancêtres.</p>
<?php include 'footer.php'; ?>

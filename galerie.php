<?php
session_start();
include 'database.php';
$user_id = $_SESSION['user_id'];
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if ($_POST['like'] == "J'aime")
	{
		if (add_like($user_id, $_POST['montage_id'], "like") == false)
		{
			$error_message = "Not like this";
		}
	}
	else if ($_POST['like'] == "Je n'aime plus")
	{
		if (remove_like($user_id, $_POST['montage_id']) == false)
		{
			$error_message = "Not dis-like this";
		}
	}
	else if ($_POST['commenter'] == "Commenter")
	{
		$comment = htmlspecialchars($_POST['comment']);
		if (empty($comment))
		{
			$error_message = "Commentaire vide!";
		}
		else if (add_comment($user_id, $_POST['montage_id'], $comment) == false)
		{
			$error_message = "It was hidden for a reason.";
		}
	}
}

$page_title = "galerie.css";
$page_head = "<link rel='stylesheet' href='index.css'><link rel='stylesheet' href='galerie.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
	<div class="galerie">
	<?php
	$page = abs(intval($_GET['page']));
	$total = 10;
	$start = $page * $total;

	$montages = get_montages();
	$montages = array_slice($montages, $start, $total);
	foreach ($montages as $montage)
	{ ?>
		<div class='montage'>
		<?= $montage['date'] ?> | Créé par le Camagruiste <?php $creator = get_user_by_id($montage['user_id']); echo $creator['username']; ?> <br>
		<img src='<?= $montage['image']; ?>'><?php $nb_likes = count(get_likes($montage['id'])); echo ($nb_likes == 0 ?  "Personne aime ça" : $nb_likes . " point" . ($nb_likes == 1 ? "" : "s") . " d'amour"); ?>
		<form method="POST" action="galerie.php">
			<input type="hidden" name="montage_id" value="<?= $montage['id'] ?>">
		<?php if (do_i_like_this($user_id, $montage['id']) == false) { ?>
			<input type="submit" name="like" value="J'aime">
		<?php } else { ?>
			<input type="submit" name="like" value="Je n'aime plus">
		<?php } ?>
		</form>
		
		<form method="POST" action="galerie.php">
			<!--<button onclick="like(<?= $montage['id']; ?>)">J'aime</button>-->
			<textarea name="comment"></textarea>
			<input type="hidden" name="montage_id" value="<?= $montage['id'] ?>">
			<input type="submit" name="commenter" value="Commenter">
		</form>
		<?= $error_message ?>
		<?php
		$comments = get_comments($montage['id']);
		foreach($comments as $comment)
		{ ?>
			<div>Le <?= $comment['date'] ?>, <?php $uuu = get_user_by_id($comment['user_id']); echo $uuu['username']; ?> a dit: « <?= $comment['message'] ?> »</div>
		<?php } ?>
		</div>
	<?php }
	?>
	</div>
	<hr>
	<div>
	<a href="galerie.php?page=<?= $page - 1 < 0 ? 0 : $page - 1; ?>">Précédente</a>
	 Page <?= $page; ?> 
	<a href="galerie.php?page=<?= $page + 1; ?>">Suivante</a>
	</div>
</div>
<?php include 'footer.php'; ?>
</div>


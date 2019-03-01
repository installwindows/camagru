<?php
session_start();
include 'database.php';
$user_id = $_SESSION['user_id'];
$error_message = "";
$page = "";
$montage_id = "";
$montage_id_display = "";
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (!isset($_SESSION['user_id']))
		$error_message = "Vous devez vous connecter!";
	else if ($_POST['like'] == "J'aime")
	{
		$id = add_like($user_id, $_POST['montage_id'], "like");
		if ($id === false)
			$error_message = "Not like this";
		else
		{
			$data = get_like_by_id($id);
			$montage = get_montage_by_id($_POST['montage_id']);
			$target_user = get_user_by_id($montage['user_id']);
			if ($target_user['notify_like']);
				notify_user($montage['user_id'], "like", $data);
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
		else
		{
			$id = add_comment($user_id, $_POST['montage_id'], $comment);
			if ($id === false)
				$error_message = "It was hidden for a reason.";
			else
			{
				$data = get_comment_by_id($id);
				$montage = get_montage_by_id($_POST['montage_id']);
				$target_user = get_user_by_id($montage['user_id']);
				if ($target_user['notify_comment']);
					notify_user($montage['user_id'], "comment", $data);
			}
		}
	}
	if (isset($_POST['page']))
		$page = abs( intval($_POST['page']));
	if (isset($_POST['montage_id']))
		$montage_id = htmlspecialchars($_POST['montage_id']);
	if (isset($_POST['montage_id_display']))
		$montage_id_display = htmlspecialchars($_POST['montage_id_display']);
}

$page_title = "Galerie des Camagruistes!";
$page_head = "<link rel='stylesheet' href='index.css'><link rel='stylesheet' href='galerie.css'>";
?>
<?php include 'head.php'; ?>
<div class="container">
<?php include 'header.php'; ?>
<div class="main">
<?php if (isset($_GET['montage']) || !empty($montage_id_display)) { ?>
<?php
	$montage_id = !empty($montage_id_display) ? $montage_id_display : htmlspecialchars($_GET['montage']);
	$montage = get_montage_by_id($montage_id);
	if (!empty($montage)) { ?>
		<div>
		<div class='montage'>
		<?= $montage['date'] ?> | Créé par le Camagruiste <?php $creator = get_user_by_id($montage['user_id']); echo $creator['username']; ?> <br>
		<img src='<?= $montage['image']; ?>'><?php $nb_likes = count(get_likes($montage['id'])); echo ($nb_likes == 0 ?  "Personne aime ça" : $nb_likes . " point" . ($nb_likes == 1 ? "" : "s") . " d'amour"); ?>
		<form method="POST" action="galerie.php">
			<input type="hidden" name="montage_id" value="<?= $montage['id'] ?>">
			<input type="hidden" name="montage_id_display" value="<?= $montage['id'] ?>">
		<?php if (do_i_like_this($user_id, $montage['id']) == false) { ?>
			<input type="submit" name="like" value="J'aime">
		<?php } else { ?>
			<input type="submit" name="like" value="Je n'aime plus">
		<?php } ?>
		</form>
		
		<form method="POST" action="galerie.php">
			<textarea name="comment"></textarea>
			<input type="hidden" name="montage_id" value="<?= $montage['id'] ?>">
			<input type="hidden" name="montage_id_display" value="<?= $montage['id'] ?>">
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
		</div>
	<?php } else { ?>
		<div>
			Ce montage n'existe pas :(
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="galerie">
	<?php
	if (empty($page))
		$page = abs(intval($_GET['page']));
	$total = 5;
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
			<input type="hidden" name="page" value="<?= $page ?>">
		<?php if (do_i_like_this($user_id, $montage['id']) == false) { ?>
			<input type="submit" name="like" value="J'aime">
		<?php } else { ?>
			<input type="submit" name="like" value="Je n'aime plus">
		<?php } ?>
		</form>
		
		<form method="POST" action="galerie.php">
			<textarea name="comment"></textarea>
			<input type="hidden" name="montage_id" value="<?= $montage['id'] ?>">
			<input type="hidden" name="page" value="<?= $page ?>">
			<input type="submit" name="commenter" value="Commenter">
		</form>
		<?php
			if (!empty($montage_id) && $montage_id == $montage['id'])
				echo $error_message;
		?>
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
<?php } ?>
</div>
<?php include 'footer.php'; ?>
</div>


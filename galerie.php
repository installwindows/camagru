<?php
session_start();
include 'database.php';
$user_id = $_SESSION['user_id'];

$page_title = "galerie.css";
$page_head = "<link rel='stylesheet' href='galerie.css'>";
?>
<?php include 'head.php'; ?>
<?php include 'header.php'; ?>
<div class="galerie">
<?php
$page = abs(intval($_GET['page']));
$total = 10;
$start = $page * $total;

$montages = get_montages($total, $start);
foreach ($montages as $montage)
{?>
<div class='montage'>
<img src='<?= $montage['image']; ?>'>
<form method="POST" action="galerie.php">
	<button onclick="like(<?= $montage['id']; ?>)">J'aime</button>
	<textarea name="comment"></textarea>
	<input type="submit" value="Commenter">
</form>
</div>
<?php }
?>
</div>
<div>
<a href="galerie.php?page=<?= $page - 1 < 0 ? 0 : $page - 1; ?>">Précédente</a>
 Page <?= $page; ?> 
<a href="galerie.php?page=<?= $page + 1; ?>">Suivante</a>
</div>
<?php include 'footer.php'; ?>


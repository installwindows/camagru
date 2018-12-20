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
//TODO pagination
$page = $_GET['page'];
$total = 10;
$start = intval($page) * $total;

$montages = get_montages($total, $start);
foreach ($montages as $montage)
{
	echo "<img src='{$montage['image']}'>";
}
?>
</div>
<div>
<?= $start ?>
</div>
<?php include 'footer.php'; ?>

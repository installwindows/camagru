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
<h2>Camagru!</h2>
<h3><?php echo "$welcome_message"; ?></h3>
<?php include 'footer.php'; ?>

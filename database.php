<?php
$DB_DSN = "sqlite:database.sqlite";
function get_database_connection()
{
	global $DB_DSN;
	$pdo = new PDO($DB_DSN);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
}
?>

<?php
include "database.php";

function get_users()
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users");
		$query->execute();
		$results = $query->fetchAll();
		print_r($results);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}
get_users();
?>

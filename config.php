<?php
include "database.php";
	try {
		$pdo = get_database_connection();
		$pdo->query("CREATE TABLE IF NOT EXISTS users (
			id			INTEGER			PRIMARY KEY AUTOINCREMENT,
			email		varchar(255),
			username	varchar(255),
			password	varchar(255)
		);");
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
?>

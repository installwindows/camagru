<?php
include "database.php";
	try {
		$pdo = get_database_connection();
		$pdo->query("CREATE TABLE IF NOT EXISTS users (
			id			INTEGER			PRIMARY KEY AUTOINCREMENT,
			email		varchar(255)	UNIQUE,
			username	varchar(255)	UNIQUE,
			password	varchar(255)
		);");
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
?>

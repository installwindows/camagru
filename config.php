<?php
include "database.php";
	try {
		$pdo = get_database_connection();
		$pdo->query("CREATE TABLE IF NOT EXISTS users (
			id				INTEGER		PRIMARY KEY AUTOINCREMENT,
			email			TEXT		UNIQUE,
			username		TEXT		UNIQUE,
			password		TEXT,
			email_verified	INTEGER		DEFAULT	0
		);");
		$pdo->query("CREATE TABLE IF NOT EXISTS email_verification (
			id		TEXT,
			email	TEXT
		);");



	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
?>

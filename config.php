<?php
include "database.php";
	try {
		$pdo = new PDO($DB_DSN);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

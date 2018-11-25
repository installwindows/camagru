<?php
include "database.php";

function populate()
{
	echo create_user("alec@menard.com", "alec", "camagru", 1);
	echo create_user("Amajuscule@something.li", "Amaj", "camagru", 1);
	echo create_user("ralph@camagru.fr", "ralph", "camagru", 1);
	echo create_user("qerty@w.uwu", "Wasp", "camagru", 1);
}

try {
	$pdo = get_database_connection();
	$pdo->query("CREATE TABLE IF NOT EXISTS users (
		id				INTEGER		PRIMARY KEY AUTOINCREMENT,
		email			TEXT		UNIQUE,
		username		TEXT		UNIQUE,
		password		TEXT,
		email_verified	INTEGER		DEFAULT	0
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS email_task (
		id		TEXT	PRIMARY KEY,
		user_id	INTEGER,
		task	TEXT,
		data	TEXT
	);");



} catch (Exception $e) {
	echo $e->getMessage();
	die();
}

populate();
?>

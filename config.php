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
		email_verified	INTEGER		DEFAULT	0,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS email_task (
		id				TEXT		PRIMARY KEY,
		user_id			INTEGER,
		task			TEXT,
		data			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS montages (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		image			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS comments (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		montage_id		INTEGER,
		message			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS likes (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		montage_id		INTEGER,
		type			TEXT		NULL,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");

	



} catch (Exception $e) {
	echo $e->getMessage();
	die();
}

populate();

mkdir('montages');
?>

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
		notify_like		INTEGER		DEFAULT	1,
		notify_comment	INTEGER		DEFAULT	1,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS email_task (
		id				TEXT		PRIMARY KEY,
		user_id			INTEGER,
		task			TEXT,
		data			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP,
		CONSTRAINT fk_email_task_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS montages (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		image			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP,
		CONSTRAINT fk_montages_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS comments (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		montage_id		INTEGER,
		message			TEXT,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP,
		CONSTRAINT comments_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
		CONSTRAINT comments_montages FOREIGN KEY (montage_id) REFERENCES montages(id) ON DELETE CASCADE
	);");
	$pdo->query("CREATE TABLE IF NOT EXISTS likes (
		id				INTEGER		PRIMARY KEY,
		user_id			INTEGER,
		montage_id		INTEGER,
		type			TEXT		NULL,
		date			INTEGER		DEFAULT CURRENT_TIMESTAMP,
		CONSTRAINT likes_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
		CONSTRAINT likes_montages FOREIGN KEY (montage_id) REFERENCES montages(id) ON DELETE CASCADE
	);");
	



} catch (Exception $e) {
	echo $e->getMessage();
	die();
}

populate();

if (!file_exists('montages'))
	mkdir('montages');
?>

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

function get_user_by_email($email)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
		$query->execute(array("email" => $email));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
}

function get_user_by_username($username)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
		$query->execute(array("username" => $username));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
}

function create_user($email, $username, $password)
{
	$unique_email = false;
	$unique_username = false;
	if (empty(get_user_by_email($email)))
		$unique_email = true;
	if (empty(get_user_by_username($username)))
		$unique_username = true;
	if ($unique_username && $unique_email)
	{
		try {
			$pdo = get_database_connection();
			$query = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
			$query->execute(array(
				"email"		=> $email,
				"username"	=> $username,
				"password"	=> hash("whirlpool", $password)
			));
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
		return true;
	}
	return false;
}

function login_user($username, $password)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
		$query->execute(array("username" => $username, "password" => hash("whirlpool", $password)));
		$results = $query->fetchAll();
		return empty($results) ? false : true;
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
}

?>

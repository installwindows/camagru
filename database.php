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

function create_user($email, $username, $password, $verified = 0)
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
			$query = $pdo->prepare("INSERT INTO users (email, username, password, email_verified) VALUES (:email, :username, :password, :verified)");
			$query->execute(array(
				"email"		=> $email,
				"username"	=> $username,
				"password"	=> hash("whirlpool", $password),
				"verified" => $verified
			));
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
		return true;
	}
	return false;
}

function check_user($username, $password)
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

function insert_email_id($email, $id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO email_verification VALUES (:id, :email)");
		$query->execute(array("id" => $id, "email" => $email));
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}

}

function set_email($old_email, $new_email)
{
	$user = get_user_by_email($old_email);
	if (empty($user) || !empty(get_user_by_email($new_email)))
	{
		return false;
	}
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("UPDATE users SET email = :new_email WHERE email = :old_email");
		$query->execute(array("new_email" => $new_email, "old_email" => $old_email));
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
	return true;
}

?>

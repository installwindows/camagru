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

function get_user_by_id($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
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
			return $pdo->lastInsertId();
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
	}
	return false;
}

function authenticate_user($username, $password)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
		$query->execute(array("username" => $username, "password" => hash("whirlpool", $password)));
		$results = $query->fetchAll();
		if (!empty($results))
			return $results[0];
		return false;
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

function send_task($user_id, $task, $data = "")
{
	$user = get_user_by_id($user_id);
	$email = $user['email'];
	$message = "";
	$subject = "";
	$headers = "Content-Type: text/html; charset=UTF-8\r\n";
	$id = hash('whirlpool', time() . rand() . $email);
	$verification_url = "http://" . $_SERVER['HTTP_HOST'] . "/email.php?id=" . $id;

	switch ($task)
	{
	case "inscription_email":
		$subject = "Vérification compte Camagru";
		$message = "<h1>Bienvenue sur Camagru!</h1>Vérifiez votre courriel en utilisant ce lien: ";
		$message .= "<a href='$verification_url'>C'est moi le lien!</a>";
		break;
	case "change_email":
		$email = $data;
		$subject = "ALLO ICI CAMAGRULL! Changement de EMAILE!";
		$message = "Cliquez sur l'unique lien de ce courriel pour changer votre mot de passe.<br>";
		$message .= "<a href='$verification_url'>Je suis l'unique lien de ce courriel.</a>";
		break;
	case "forget_password":
		break;
	}
	mail($email, $subject, $message, $headers);
	$pdo = get_database_connection();
	$query = $pdo->prepare("INSERT INTO email_task VALUES (:id, :user_id, :task, :data)");
	$query->execute(array("id" => $id, "user_id" => $user['id'], "task" => $task, "data" => $data));
}

?>

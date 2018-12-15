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

function change_username($user_id, $new_username)
{
	if (!empty(get_user_by_username($new_username)))
	{
		return false;
	}
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("UPDATE users SET username = :new_username WHERE id = :user_id");
		$query->execute(array("new_username" => $new_username, "user_id" => $user_id));
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
	return true;
}

function change_password($user_id, $new_password)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
		$query->execute(array("new_password" => $new_password, "user_id" => $user_id));
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
	return true;
}

function change_email($user_id, $new_email)
{
	if (!empty(get_user_by_email($new_email)))
	{
		return false;
	}
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("UPDATE users SET email = :new_email WHERE id = :user_id");
		$query->execute(array("new_email" => $new_email, "user_id" => $user_id));
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
	case "password_lost":
		$verification_url = "http://" . $_SERVER['HTTP_HOST'] . "/oublie.php?id=" . $id;
		$subject = "{$user['username']}, mot de passe oublié";
		$message = "<p>Résultat de notre recherche: que dalle</p>Activez ceci: &#10087; <a href='$verification_url'>HYPERLIEN!</a> &#9753; pour le remplacer.";
		break;
	}
	mail($email, $subject, $message, $headers);
	$pdo = get_database_connection();
	$query = $pdo->prepare("INSERT INTO email_task VALUES (:id, :user_id, :task, :data)");
	$query->execute(array("id" => $id, "user_id" => $user['id'], "task" => $task, "data" => $data));
}

function get_email_task($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM email_task WHERE id = :id");
		$query->execute(array("id" => $id));
		$result = $query->fetch();
		if (!empty($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function remove_task($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("DELETE FROM email_task WHERE id = :id");
		$query->execute(array("id" => $id));
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function create_new_montage($pic, $bg, $user_id)
{
	$b64 = substr($pic, strpos($pic, ',') + 1);
	$image = base64_decode($b64);
	$image_url = "montages/" . time() . ".png";
	file_put_contents($image_url, $image);
	$metadata = getimagesize($image_url);
	if ($metadata === false || $metadata['mime'] !== "image/png")
	{
		unlink($image_url);
		return false;
	}
	//TODO MERGE PIC AND BG
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO montages (user_id, image) VALUES (:user_id, :image_url)");
		$query->execute(array("user_id" => $user_id, "image_url" => $image_url));
		return $pdo->lastInsertId();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function get_montages($user_id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM montages WHERE user_id = :user_id");
		$query->execute(array("user_id" => $user_id));
		$result = $query->fetchAll();
		return $result;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

?>

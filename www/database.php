<?php
$DB_DSN = "sqlite:".dirname(__FILE__)."/../db.sqlite";

function get_database_connection()
{
	global $DB_DSN;
	$pdo = new PDO($DB_DSN);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec("PRAGMA foreign_keys=ON");
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
	$headers = 
		'From: no-reply@camagru.art' . "\r\n" .
		'Content-Type: text/html; charset=UTF-8' . "\r\n" .
		'X-Mailer: PHP/'.phpversion();
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
		$subject = "Camagru: Vous voulez changer d'adresse email?";
		$message = "Cliquez sur l'unique lien de ce courriel pour changer votre mot de passe.<br>";
		$message .= "<a href='$verification_url'>Je suis l'unique lien de ce courriel.</a>";
		break;
	case "password_lost":
		$verification_url = "http://" . $_SERVER['HTTP_HOST'] . "/oublie.php?id=" . $id;
		$subject = "Camagruiste {$user['username']}, mot de passe oublié?";
		$message = "<p>Résultat de notre recherche: Une longue chaîne de caractère hexadécimale ne faisant aucun sens...</p>Activez ceci: &#10087; <a href='$verification_url'>HYPERLIEN!</a> &#9753; pour le remplacer.";
		break;
	}
	mail($email, $subject, $message, $headers);
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO email_task (id, user_id, task, data) VALUES (:id, :user_id, :task, :data)");
		$query->execute(array("id" => $id, "user_id" => $user['id'], "task" => $task, "data" => $data));
	} catch (Exception $e) {
		echo $e->getMessage();
	}
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
		//TODO remove likes and comments too
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function create_new_montage($image_url, $filter, $user_id)
{
	$img = imagecreatefromstring(file_get_contents($image_url));
	if ($img === false)
		return false;
	$img = imagescale($img, 640, 480);
	imagepng($img, "output.png");
	$source = imagecreatefrompng(dirname(__FILE__)."/images/$filter");
	if ($source === false)
		return false;
	//$dest = imagecreatefrompng($image_url);
	$dest = imagecreatefrompng("output.png");
	if ($dest === false)
		return false;
	imagealphablending($source, true);
	imagesavealpha($source, true);
	imagecopy($dest, $source, 0, 0, 0, 0, 640, 480);
	$url = "montages/" . time() . ".png";
	imagepng($dest, $url);
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO montages (user_id, image) VALUES (:user_id, :image_url)");
		$query->execute(array("user_id" => $user_id, "image_url" => $url));
		return $pdo->lastInsertId();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function remove_montage($montage_id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("DELETE FROM montages WHERE id = :montage_id");
		$query->execute(array(
			"montage_id"	=> $montage_id
		));
		return true;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function get_montage_by_id($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM montages WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function get_montages_by_user_id($user_id)
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

function get_montages()
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM montages ORDER BY date DESC");
		$query->execute();
		$result = $query->fetchAll();
		return $result;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function get_comments($montage_id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM comments WHERE montage_id = :montage_id ORDER BY date DESC");
		$query->execute(array("montage_id" => $montage_id));
		$result = $query->fetchAll();
		return $result;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function get_comment_by_id($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM comments WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function add_comment($user_id, $montage_id, $message)
{
	if (empty(get_user_by_id($user_id)) || empty(get_montage_by_id($montage_id)))
		return false;
	if (empty($message))
		return false;
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO comments (user_id, montage_id, message) VALUES (:user_id, :montage_id, :message)");
		$query->execute(array(
			"user_id"		=> $user_id,
			"montage_id"	=> $montage_id,
			"message"		=> $message
		));
		return $pdo->lastInsertId();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function get_likes($montage_id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM likes WHERE montage_id = :montage_id");
		$query->execute(array("montage_id" => $montage_id));
		$result = $query->fetchAll();
		return $result;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function get_like_by_id($id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM likes WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		return empty($results) ? $results : $results[0];
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function do_i_like_this($user_id, $montage_id)
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM likes WHERE user_id = :user_id AND montage_id = :montage_id");
		$query->execute(array("user_id" =>$user_id, "montage_id" => $montage_id));
		$result = $query->fetchAll();
		return !empty($result);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function add_like($user_id, $montage_id, $type)
{
	if (empty(get_user_by_id($user_id)) || empty(get_montage_by_id($montage_id)))
		return false;
	if (empty($type))
		return false;
	if (!empty(do_i_like_this($user_id, $montage_id)))
		return false;
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("INSERT INTO likes (user_id, montage_id, type) VALUES (:user_id, :montage_id, :type)");
		$query->execute(array(
			"user_id"		=> $user_id,
			"montage_id"	=> $montage_id,
			"type"		=> $type
		));
		return $pdo->lastInsertId();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function remove_like($user_id, $montage_id)
{
	if (empty(get_user_by_id($user_id)) || empty(get_montage_by_id($montage_id)))
		return false;
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND montage_id = :montage_id");
		$query->execute(array(
			"user_id"		=> $user_id,
			"montage_id"	=> $montage_id
		));
		return true;
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	return false;
}

function update_user_notify($user_id, $type, $value)
{
	try {
		$pdo = get_database_connection();
		if ($type == "like")
			$query = $pdo->prepare("UPDATE users SET notify_like = :value WHERE id = :user_id");
		else if ($type == "comment")
			$query = $pdo->prepare("UPDATE users SET notify_comment = :value WHERE id = :user_id");
		else
			return false;
		$query->execute(array("value" => $value, "user_id" => $user_id));
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
	return true;
}

function notify_user($user_id, $type, $data)
{
	$subject = "";
	$message = "";
	$user = get_user_by_id($user_id);
	$user2 = get_user_by_id($data['user_id']);
	if ($type == "like")
	{
		$subject = "{$user2['username']} aime l'un de vos montages!";
		$message = "Votre <a href=''>montage</a> est aimé par le Camagruiste {$user2['username']}!";
	}
	else if ($type == "comment")
	{
		$subject = "{$user2['username']} a laissé un commentaire sur l'un de vos montages!";
		$message = "Je cite: « {$data['message']} » - {$user2['username']}, faisant référence à votre <a href=''>montage</a>.";
	}
	else
		return false;
	$headers = 
		'From: no-reply@camagru.art' . "\r\n" .
		'Content-Type: text/html; charset=UTF-8' . "\r\n" .
		'X-Mailer: PHP/'.phpversion();
	$email = $user['email'];
	mail($email, $subject, $message, $headers);
	return true;
}
?>

<?php
session_start();
function handle_login($post)
{
	$username = $post["username"];
	$password = $post["password"];
	$response = [];
	//if ($user = authenticate_user($post["username"], $post["password"]))
	if (($user = ['id' => 42, 'email_verified' => true]) || true)
	{
		if ($user['email_verified'])
		{
			$_SESSION['user_id'] = $user['id'];
			$response['result'] = 'ok';
		}
		else
		{
			$response['error'][] = "Adresse courriel non-validée.";
		}
	}
	else
	{
		$response['error'][] = "Nom d'utilisateur et/ou mot de passe invalide.";
	}
	return $response;
}

function handle_logout()
{
	session_destroy();
	return ['result' => 'ok'];
}
?>

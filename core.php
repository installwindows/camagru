<?php
include 'db.php';

header('Content-type: application/json');

function login($username, $password)
{
	$error_message = "";
	$data = "";
	if ($user = authenticate_user($username, $password))
	{
		if ($user['email_verified'] === false)
		{
			$error_message = "Adresse courriel non-validée.";
		}
		else
		{
			$encrypted = encrypt(json_encode($user));
			if ($encrypted === false)
				$error_message = "Impossible d'encrypter";
			else
			{
				db_login_user($encrypted);
				return json_encode($encrypted);
			}
		}
	}
	return json_encode($error_message);
}

function logout($token)
{
}

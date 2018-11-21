<?php
function validate_email($email)
{
	return filter_var($email, FILTER_VALIDATE_EMAIL) === false ? false : true;
}

/* Username are at least 3 characters long and less than 32. They can only use alphanumeric characters. */
function validate_username($username)
{
	if (strlen($username) < 3 || strlen($username) > 32)
		return false;
	if (!ctype_alnum($username))
		return false;
	return true;
}

function validate_password($password)
{
	if (strlen($password) > 255)
		return false;
	return true;
}
?>

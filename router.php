<?php
if (php_sapi_name() === 'cli-server')
{
	/*
	echo 'SCRIPT_FILENAME: '.$_SERVER['SCRIPT_FILENAME'] . "<br>";
	echo 'DOCUMENT_ROOT..: '.$_SERVER['DOCUMENT_ROOT'] . "<br>";
	echo 'SCRIPT_NAME....: '.$_SERVER['SCRIPT_NAME'] . "<br>";
	echo 'REQUEST_URI....: '.$_SERVER['REQUEST_URI'] . "<br>";
	echo dirname($_SERVER['SCRIPT_NAME']) . "<br>";
	 */
	$requested_file = $_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['SCRIPT_NAME'];
	$requested_file_type = pathinfo($requested_file, PATHINFO_EXTENSION);
	if ($requested_file_type === 'sqlite' || basename($requested_file) == 'router.php' || strpos($requested_file, 'config') !== false || strpos($requested_file, '..') !== false)
	{
		header("Refresh:3; url=index.php");
		echo "Vous n'avez pas le droit! Oust!";
		die();
	}
	else if (is_file($requested_file))
	{
		if ($requested_file_type === 'php')
			include $requested_file;
		else
			return false;
	}
	else
	{
		header("Refresh:3; url=index.php");
		echo "Ce document est introuvable!";
		die();
	}
}

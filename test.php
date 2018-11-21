<?php
include "database.php";

function get_users()
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users");
		$query->execute();
		$results = $query->fetchAll();
		print_r($results);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

#create_user("alec@menard.com", "alec", "abc123");
#get_users();

#echo create_user("alec@menard.comm", "alecc", "abc123") ? "created" : "not created";
echo login_user("aleccc", "abc123") ? "loged" : "not loged";
?>

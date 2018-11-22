<?php
include "database.php";

function get_users()
{
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM users");
		$query->execute();
		$results = $query->fetchAll();
		foreach ($results as $user)
		{
			echo "<b>email</b>: " . $user["email"] . "<br>";
			echo "<b>verified</b>: " . $user["email_verified"] . "<br>";
			echo "<b>username</b>: " . $user["username"] . "<br>";
			echo "<b>password</b>: " . substr($user["password"], 0, 8) . "..." . substr($user["password"], 120);
			echo "<hr>";
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

#create_user("alec@menard.com", "alec", "abc123");
#get_users();

#echo create_user("alec@menard.comm", "alecc", "abc123") ? "created" : "not created";
#echo login_user("aleccc", "abc123") ? "loged" : "not loged";


$headers = "Content-Type: text/html; charset=UTF-8\r\n";
mail("varnaud@live.ca", "Hello from php", "Bye <b>bye</b>", $headers);
?>

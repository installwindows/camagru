<?php
include 'database.php';
if (isset($_GET["id"]))
{
	$id = $_GET["id"];
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM email_verification WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		if (!empty($results))
		{
			$email = $results[0]["email"];
			$query = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE email = :email");
			$query->execute(array("email" => $email));
			$query = $pdo->prepare("DELETE FROM email_verification WHERE id = :id");
			$query->execute(array("id" => $id));
			header("Refresh:3; url=index.php");
			echo "Courriel validé.";
		}
		else
			header("Location: index.php");
	} catch (Exception $e) {
		echo $e->getMessage();
		die();
	}
}
else
{
	header("Location: index.php");
}
?>

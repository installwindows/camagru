<?php
include 'database.php';
if (isset($_GET["id"]))
{
	$id = $_GET["id"];
	try {
		$pdo = get_database_connection();
		$query = $pdo->prepare("SELECT * FROM email_task WHERE id = :id");
		$query->execute(array("id" => $id));
		$results = $query->fetchAll();
		if (!empty($results))
		{
			$user = get_user_by_id($results[0]['user_id']);
			$data = $results[0]["data"];
			switch ($results[0]['task'])
			{
			case "change_email":
				change_email($user['id'], $data);
				echo "Courriel mis à jour";
				break;
			case "inscription_email":
				$query = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE id = :user_id");
				$query->execute(array("user_id" => $user['id']));
				echo "Courriel validé.";
				break;
			case "forget_password": ?>
				<form method="POST" action="oublie.php">
					Entrez le nouveau mot de passe: <input type="password" name="password"><br>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="submit" value="Confirmer">
				</form><?php
				die();
				break;
			}
			$query = $pdo->prepare("DELETE FROM email_task WHERE id = :id");
			$query->execute(array("id" => $id));
			header("Refresh:3; url=index.php");
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

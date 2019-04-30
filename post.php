<?php
include 'php.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$result = ['error' => ['Formulaire invalide']];
	if (isset($_POST['submit']))
	{
		switch ($_POST['submit'])
		{
		case 'login':
				$result = handle_login($_POST);
			break;
		case 'logout':
				$result = handle_logout($_POST);
			break;
		case 'montage':
				$result = handle_montage($_POST);
			break;
		default:
				$result = ['error' => ['Bad request']];
		//TODO THE REST
		}
	}
	$result['submit'] = $_POST;
	echo json_encode($result);
}
?>

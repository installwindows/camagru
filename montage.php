<?php
include 'database.php';
session_start();
if (isset($_SESSION["user_id"]))
{
	$user = get_user_by_id($_SESSION["user_id"]);
}
else
{
	header("Location: connexion.php");
	die();
}
$error_message = "";
$upload_error = "";
$upload_success = "";
$select_error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (isset($_POST['remove_montage']))
	{
		$montage_to_delete_id = $_POST['montage_to_delete'];
		$montage = get_montage_by_id($montage_to_delete_id);
		if (!empty($montage) && $montage['user_id'] == $user['id'])
		{
			remove_montage($montage['id']);
		}
		else
		{
			$error_message = "Impossible de delet ce montage :(";
		}
	}
	else
	{
		//print_r($_POST);
		$filter = htmlspecialchars($_POST['radio_image']);
		$image_url = "";
		if (empty($_POST['radio_image']))
			$select_error = "Sélectionnez un filtre.";
		else if (!file_exists(dirname(__FILE__)."/images/$filter"))
			$error_message = "Filtre invalide!";
		else if (isset($_POST['file_form']))
		{
			if (isset($_FILES['upload_image']) && $_FILES['upload_image']['error'] === UPLOAD_ERR_OK)
				$image_url = $_FILES['upload_image']['tmp_name'];
			else if (isset($_FILES['upload_image']) && $_FILES['upload_image']['error'] > 0)
			{
				$error_message = "Impossible d'utiliser ce fichier!";
			}
		}
		else if (isset($_POST['webcam_form']))
		{
			$canvas = htmlspecialchars($_POST['picture']);
			$b64 = substr($canvas, strpos($canvas, ',') + 1);
			$image = base64_decode($b64);
			//$image_url = "montages/" . time() . ".png";
			$image_url = dirname(__FILE__)."/montages_tmp" . time() . ".png";
			file_put_contents($image_url, $image);
		}
		else
		{
			$error_message = "Utilisez un formulaire!";
		}
		if (!empty($image_url))
		{
			if (create_new_montage($image_url, $filter, $user['id']))
				$upload_success = "This was a triumph &#x266b;";
			else
				$upload_error = "Could not upload the image. Please try again senpai!";
			unlink($image_url);
		}
	}
}
$page_title = "Montage";
$page_head = "<link rel='stylesheet' href='montage.css'>";
?>
<?php include 'head.php' ?>
<div class='container'>
<?php include 'header.php' ?>
<div class='main'>
<div id="webcam_available" hidden>
	<div class="webcam">
		<div class="overlay_box">
			<div class="overlay"></div>
			<video autoplay="true" id="videoElement"></video><br>
		</div>
	</div>
	<canvas id="canvas" width="640" height="480" hidden></canvas>
	<button id="snap" onclick="upload_montage()">SNAP!</button>
	<form id="webcam_form" method="POST" action="montage.php" enctype='multipart/form-data'>
		<input type="hidden" name="picture" value="">
		<input name="webcam_form" type='hidden' value='ok'>
	</form>
</div>
<div id="webcam_unavailable" hidden>
	<form id="file_form" method="POST" action="montage.php" enctype='multipart/form-data'>
		Sélection d'une image: <input type='file' name='upload_image'><br>
		<input name="file_form" type='submit' value='FUSION!'>
	</form>
</div>
<div id="error_message">
<?= $error_message ?>
<span class="error" id="upload_error"><?= $upload_error ?></span>
<span class="success" id="upload_success"><?= $upload_success ?></span>
</div>
<div class="image_list">
	<span id="select_error"></span><br>
	<?php
	$files = array_diff(scandir(dirname(__FILE__).'/images'), ['.', '..']);
	foreach ($files as $file) { ?>
		<label for="radio<?= $file ?>"><input form="" type="radio" name="radio_image" value="<?= $file ?>" id="radio<?= $file ?>"><img class="lst_img" src="images/<?= $file ?>" height="120" width="160" onclick="document.querySelector('.overlay').style.backgroundImage = 'url(\'images/<?= $file ?>\')';"></label>
	<?php } ?>
</div>
</div>
<div class='side'>
<?php
	$montages = get_montages_by_user_id($user['id']);
	foreach (array_reverse($montages) as $montage)
	{ ?>
		<img src='<?= $montage['image'] ?>'>
		<form method="POST" action="montage.php">
			<input name="montage_to_delete" type="hidden" value="<?= $montage['id'] ?>">
			<input name="remove_montage" type="submit" value="delet">
		</form>
	<?php }
?>
</div>
<script>
var videoElement = document.getElementById("videoElement");

if (navigator.mediaDevices.getUserMedia)
{       
	navigator.mediaDevices.getUserMedia({video: true}).then(function(stream) {
		//Webcam available
		videoElement.srcObject = stream;
		document.getElementById('webcam_available').removeAttribute('hidden', '');
		var radio = document.getElementsByName('radio_image');
		radio.forEach(function(r){
			r.setAttribute('form', 'webcam_form');
		});
	}).catch(function(err0r) {
		//Webcam not available
		document.getElementById('webcam_unavailable').removeAttribute('hidden', '');
		var radio = document.getElementsByName('radio_image');
		radio.forEach(function(r){
			r.setAttribute('form', 'file_form');
		});
	});
}
</script>
<script>
function upload_montage()
{
	var form = document.forms['webcam_form'];
	var canvas = document.getElementById('canvas');
	var context = canvas.getContext('2d');
	var video = document.getElementById('videoElement');
	var radio = document.querySelectorAll('input[type=radio]:checked');
	if (canvas && radio.length)
	{
		context.drawImage(video, 0, 0, 640, 480);
		var photo = canvas.toDataURL();
		form.elements['picture'].value = photo;
		form.submit();
	}
	else
	{
		document.getElementById('select_error').innerHTML = "Sélectionnez au moins une image.";
	}
}
</script>
<?php include 'footer.php' ?>

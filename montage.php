<?php
session_start();
include 'database.php';
$user_id = $_SESSION['user_id'];
if (empty($user_id))
{
	header("Location: connexion.php");
	die();
}

$upload_error = "";
$upload_success = "";
$select_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$image = htmlspecialchars($_POST['radio_image']);
	$canvas = htmlspecialchars($_POST['picture']);
	//TODO check if image exist
	//echo "radio: " . $image . "<br>";
	//echo "picture: " . $canvas;
	//die();
	if (create_new_montage($canvas, $image, $user_id))
	{
		$upload_success = "HEY HEY YOU YOU CREATED A NEW MONTAGE &#x266b;";
	}
	else
	{
		$upload_error = "Could not upload the image. Please try again senpai!";
	}
}
$page_title = "Montage";
$page_head = "<link rel='stylesheet' href='montage.css'>";
?>
<?php include 'head.php' ?>
<div class='container'>
<?php include 'header.php' ?>
	<div class='main'>
		<h1>Montage</h1>
		<form name="alpha" method="POST" action="montage.php">
			<div class="webcam">
				<div class="overlay_box">
					<div class="overlay"></div>
					<video id="video" width="640" height="480" autoplay></video><br>
				</div>
				<br>
				<button id="snap" onclick="upload_montage()">SNAP!</button>
				<span class="error" id="upload_error"><?= $upload_error ?></span>
				<span class="success" id="upload_success"><?= $upload_success ?></span>
			</div>
			<canvas id="canvas" width="640" height="480"></canvas>
			<input type="hidden" name="picture" value="">
			<div class="image_list">
				<span id="select_error"></span><br>
				<?php
				$files = array_diff(scandir('images'), ['.', '..']);
				foreach ($files as $file) { ?>
					<label for="radio<?= $file ?>"><input type="radio" name="radio_image" value="<?= $file ?>" id="radio<?= $file ?>"><img class="lst_img" src="images/<?= $file ?>" height="120" width="160" onclick="document.querySelector('.overlay').style.backgroundImage = 'url(\'images/<?= $file ?>\')';"></label>
				<?php } ?>
			</div>
		</form>
	</div>
	<div class='side'>
		side
	</div>
<?php include 'footer.php' ?>

<script>
function upload_montage()
{
	var form = document.forms['alpha'];
	var canvas = document.getElementById('canvas');
	var context = canvas.getContext('2d');
	var video = document.getElementById('video');
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
<script>
var video = document.getElementById('video');
/*
var no_webcam = `
		<form method='POST' action='montage.php' enctype='multipart/form-data'>
			Image: <input type='file' name='file_image'><br>
			<input type='submit' value='FUSION!'>
		</form>
	`;
 */
if(!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia))
{
	document.querySelector('.webcam').innerHTML = "No webcam for you. Utilisez Firefox pour avoir accès à la caméra<br>" + no_webcam;
}
else
{
	navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
		video.srcObject = stream;
		video.play();
	}).catch (function(err){
		document.querySelector('.webcam').innerHTML = "Aucune webcam disponible.<br>" + no_webcam;
	});
}
</script>

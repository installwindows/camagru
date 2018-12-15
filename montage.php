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
		$upload_success = "This was a triumph &#x266b;";
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
					<video autoplay="true" id="videoElement"></video><br>
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
<?php
		$montages = get_montages($user_id);
		foreach ($montages as $montage)
		{
			echo "<img src='{$montage['image']}'>";
		}
?>
	</div>

<script>
/*
var no_webcam = `
		<form method='POST' action='montage.php' enctype='multipart/form-data'>
			Image: <input type='file' name='file_image'><br>
			<input type='submit' value='FUSION!'>
		</form>
	`;
 */
		var videoElement = document.getElementById("videoElement");

				console.log("video");
		if (navigator.mediaDevices.getUserMedia) {       
			navigator.mediaDevices.getUserMedia({video: true}).then(function(stream) {
				videoElement.srcObject = stream;
			}).catch(function(err0r) {
				console.log("No stream!");
			});
		}
</script>
<?php include 'footer.php' ?>

<script>
function upload_montage()
{
	var form = document.forms['alpha'];
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

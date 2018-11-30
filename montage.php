<?php
session_start();
if (empty($_SESSION['user_id']))
{
	header("Location: index.php");
	die();
}
$page_title = "Photo Montage";
$page_head = "<link rel='stylesheet' href='montage.css'>";
?>
<?php include 'head.php' ?>
<div class='container'>
<?php include 'header.php' ?>
	<div class='main'>
		<h1>Montage</h1>
		<form method="POST" action="montage.php">
			<div class="webcam">
				<div class="overlay_box">
					<div class="overlay"></div>
					<video id="video" width="640" height="480" autoplay></video><br>
				</div>
				<button id="snap" onclick="upload_montage()">SNAP!</button>
			</div>
			<canvas id="canvas" width="640" height="480"></canvas>
			<div class="image_list">
				<span id="select_error"></span><br>
				<?php
				$files = array_diff(scandir('images'), ['.', '..']);
				foreach ($files as $file) { ?>
					<label for="<?= $file ?>"><input onchange="checkbox_onchange(this)" type="checkbox" name="<?= $file ?>" id="<?= $file ?>"><img class="lst_img" src="images/<?= $file ?>" height="120" width="160" onclick="document.querySelector('.overlay').style.backgroundImage = 'url(\'images/<?= $file ?>\')';"></label>
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
	var canvas = document.getElementById('canvas');
	var checked = document.querySelectorAll('input[type=checkbox]:checked');
	if (canvas && checked.length)
	{
		var photo = canvas.toDataURL();
		console.log(checked);
		document.getElementById('select_error').innerHTML = "";
		//POST to montage.php
	}
	else
	{
		document.getElementById('select_error').innerHTML = "Sélectionnez au moins une image.";
	}
}

function checkbox_onchange(checkbox)
{
	var checked = document.querySelectorAll('input[type=checkbox]:checked');
	if (checked.length)
	{
		for (var i = 0; i < checked.length; i++)
		{
			checked[i].checked = false;
		}
	}
	checkbox.checked = true;
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
var canvas = document.getElementById('canvas');
var context = canvas.getContext('2d');
var video = document.getElementById('video');
document.getElementById("snap").addEventListener("click", function() {
	context.drawImage(video, 0, 0, 640, 480);
});
</script>

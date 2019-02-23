<div id="webcam_available" hidden>
	Webcam
	<div class="webcam">
		<div class="overlay_box">
			<div class="overlay"></div>
			<!--<video autoplay="true" id="videoElement"></video><br>-->
		</div>
	</div>
	<canvas id="canvas" width="640" height="480"></canvas>
	<button id="snap" onclick="upload_montage()">SNAP!</button>
	<span class="error" id="upload_error"><?= $upload_error ?></span>
	<span class="success" id="upload_success"><?= $upload_success ?></span>
</div>
<form name="alpha" method="POST" action="montage.php" enctype='multipart/form-data'>
	<input type="hidden" name="picture" value="">
	<div id="webcam_unavailable" hidden>
		Image: <input type='file' name='file_image'><br>
		<input type='submit' value='FUSION!'>
	</div>
	<div class="image_list">
		<span id="select_error"></span><br>
		<?php
		$files = array_diff(scandir('images'), ['.', '..']);
		foreach ($files as $file) { ?>
			<label for="radio<?= $file ?>"><input type="radio" name="radio_image" value="<?= $file ?>" id="radio<?= $file ?>"><img class="lst_img" src="images/<?= $file ?>" height="120" width="160" onclick="document.querySelector('.overlay').style.backgroundImage = 'url(\'images/<?= $file ?>\')';"></label>
		<?php } ?>
	</div>
</form>
<script>
var videoElement = document.getElementById("videoElement");

if (navigator.mediaDevices.getUserMedia)
{       
	navigator.mediaDevices.getUserMedia({video: true}).then(function(stream) {
		//Webcam available
		videoElement.srcObject = stream;
		document.getElementById('webcam_available').removeAttribute('hidden', '');
	}).catch(function(err0r) {
		//Webcam not available
		document.getElementById('webcam_unavailable').removeAttribute('hidden', '');
		console.log('hinndnde');
	});
}
</script>
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

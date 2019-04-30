<?php
session_start();
?>
<h2>Connexion</h2>
<form method="POST" id="login_form">
	Nom d'utilisateur: <input type="text" name="username" value=""><br>
	Mot de passe: <input type="password" name="password" value=""><br>
	<input type="hidden" name="submit" value="login"><br>
</form>
	<button onclick="login()">Connexion</button>
<div id="response_text">
</div>
<script>
<!--
function login() {
	var form = document.forms['login_form'];
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("response_text").innerHTML = this.responseText;
		}
	};
	xhttp.open("POST", "post.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("submit=" + form['submit'].value + "&username=" + form['username'].value + "&password=" +  form['password'].value);
}
//-->
</script>

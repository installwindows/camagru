<?php
$to = "camagru@cock.li";
$subject = "This is a test email";
$message = "This is the <b>content</b> of the email";
$headers = 
	'From: no-reply@camagru.art' . "\r\n" .
	'Content-Type: text/html; charset=UTF-8' . "\r\n" .
	'X-Mailer: PHP/'.phpversion();
echo mail($to, $subject, $message, $headers) . "\n";
?>

<?php
function encrypt($data, $key)
{
	if (empty($data) || empty($key))
		return false;
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('blowfish'));
	return base64_encode($iv."::".openssl_encrypt($data, "blowfish", $key, 0, $iv));
}

function decrypt($data, $key)
{
	if (empty($data) || empty($key))
		return false;
	$data = base64_decode($data);
	if ($data === false)
		return false;
	list($iv, $message) = explode('::', $data, 2);
	return openssl_decrypt($message, "blowfish", $key, 0, $iv);
}

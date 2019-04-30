<?php
include('crypt.php');
$key = "key";
$data = "hello world";
$encrypted = encrypt($data, $key);
echo $encrypted."\n";
$decrypted = decrypt($encrypted, $key);
echo $decrypted."\n";

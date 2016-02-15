<?php

// Source: http://stackoverflow.com/a/8232171/195722
function aes_encrypt($key, $text) {
	$realKey = "";
	if (count($key) < 32) { //if it's less than 32 bits - pad it
		$realKey =  str_pad($key, 32 - count($key) + 1);
	} else if (count($key) > 32) {
		throw new \Exception("Key is too long");
	} else {
		$realKey = $key;
	}

	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $realKey, $text, MCRYPT_MODE_ECB));
}

function aes_decrypt($key, $cipherText) {
	$realKey = "";
	if (count($key) < 32) { //if it's less than 32 bits - pad it
		$realKey = str_pad($key, 32 - count($key) + 1);
	} else if (count($key) > 32) {
		throw new \Exception("Key is too long");
	} else {
		$realKey = $key;
	}
	return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $realKey, base64_decode($cipherText), MCRYPT_MODE_ECB);
}
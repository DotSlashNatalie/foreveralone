<?php

use \phpoauthlib2\providers\GoogleAuthProvider;
use \phpoauthlib2\OAuth;

require 'OAuth.php';
require 'providers/GoogleAuthProvider.php';

$authProvider = new GoogleAuthProvider($_GET, [
    "client_id" => "apps.googleusercontent.com",
    "client_secret" => "<KEY>",
    "redirect_uri" => "http://example.com/phpoauthlib2/example.php"
]);

$oauth = new OAuth($authProvider, $_GET);

$check = $oauth->check();

if ($check === true) {
    echo "Hello - " . $authProvider->getFirstName();
    echo "<br>Your email is - " . $authProvider->getEmail();
} else {
    header("Location: " . $check);
}
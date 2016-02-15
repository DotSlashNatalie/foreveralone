<?php

namespace system\vendor\phpoauthlib2\providers;

require "OAuthDataProvider.php";

use phpoauthlib2\ccurl;
use phpoauthlib2\OAUTH_SCOPES;
use phpoauthlib2\OAuthDataProvider;

class FacebookAuthProvider extends OAuthDataProvider {

    public function __construct($request, $conf, $scopes=[OAUTH_SCOPES::EMAIL]) {
        parent::__construct(
            "https://graph.facebook.com/me",
            "https://www.facebook.com/dialog/oauth",
            "https://graph.facebook.com/oauth/access_token",
            $request
        );

        $this->client_secret = $conf["client_secret"];
        $this->redirectURL = $conf["redirect_uri"];
        $this->clientId = $conf["client_id"];
        $tempScopes = [];
        foreach($scopes as $scope) {
            switch ($scope) {
                case OAUTH_SCOPES::EMAIL:
                    $tempScopes[] = "email";
            }
        }
        $tempScopes[] = "public_profile";
        $this->scope = implode(" ", $tempScopes);
    }

    public function getEmail() {
        return $this->profileData["email"];
    }

    public function getFirstName() {
        return $this->profileData["first_name"];
    }

    public function getLastName() {
        return $this->profileData["last_name"];
    }

    public function getId() {
        return $this->profileData["id"];
    }

    public function getSource() {
        return "FACEBOOK";
    }

    public function parseToken() {
        $token = $this->getToken();
        return explode("=", $token)[1];
    }

    public function getProfile() {
        $token = $this->parseToken();
        $profileUrl = $this->profile . "?fields=first_name,last_name,name,email,age_range&access_token=" . $token;
        $curl = new ccurl($profileUrl);
        $curl->createCurl();
        $ret = (string)$curl;
        $this->profileData = json_decode($ret, true);
        return $ret;
    }

}
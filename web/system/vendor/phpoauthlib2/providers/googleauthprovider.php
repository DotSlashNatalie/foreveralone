<?php

namespace system\vendor\phpoauthlib2\providers;

use phpoauthlib2\ccurl;
use system\vendor\phpoauthlib2\OAUTH_SCOPES;
use system\vendor\phpoauthlib2\OAuth;
use system\vendor\phpoauthlib2\OAuthDataProvider;

class GoogleAuthProvider extends OAuthDataProvider {

    public function __construct($request, $conf, $scopes=[OAUTH_SCOPES::EMAIL]) {
        parent::__construct(
            "https://www.googleapis.com/oauth2/v1/userinfo?access_token",
            "https://accounts.google.com/o/oauth2/auth",
            "https://accounts.google.com/o/oauth2/token", $request);

        $this->client_secret = $conf["client_secret"];
        $this->redirectURL = $conf["redirect_uri"];
        $this->clientId = $conf["client_id"];

        $tmpScopes = [];
        foreach($scopes as $scope) {
            switch ($scope) {
                case OAUTH_SCOPES::EMAIL:
                    $tmpScopes[] = "https://www.googleapis.com/auth/userinfo.email";
            }
        }

        $this->scope = implode(" ", $tmpScopes);
    }

    public function getEmail() {
        return $this->profileData["email"];
    }

    public function getFirstName() {
        return $this->profileData["given_name"];
    }

    public function getLastName() {
        return $this->profileData["family_name"];
    }

    public function getGender() {
        return $this->profileData["gender"];
    }

    public function getId() {
        return $this->profileData["id"];
    }

    public function getSource() {
        return "GOOGLE";
    }
}
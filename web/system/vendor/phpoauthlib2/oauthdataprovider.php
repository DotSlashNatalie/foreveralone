<?php

namespace system\vendor\phpoauthlib2;

require "ccurl.php";

use phpoauthlib2\ccurl;

class OAUTH_SCOPES {
    const EMAIL = "EMAIL";
}

class OAuthDataProvider {
    protected $version = "2.0";
    protected $name = "undefined";
    protected $responseType = "code";
    protected $header = "Authorization: Bearer";
    protected $profile = "";
    protected $dialog = "";
    protected $nonce = null;
    protected $accessToken = null;

    protected $state = "";
    protected $redirectURL = "";
    protected $scope = "";
    protected $clientId = "";
    protected $client_secret = "";

    protected $request = null;
    protected $profileData = [];

    public function __construct($profile, $dialog, $accessToken, $request, $header="Authorization: Bearer") {
        $this->profile = $profile;
        $this->dialog = $dialog;
        $this->accessToken = $accessToken;
        $this->header = $header;
        $this->request = $request;
    }

    public function getLoginUrl() {
        $urlBuilder = [];
        $urlBuilder[] = "client_id=" . $this->clientId;
        $urlBuilder[] = "response_type=" . $this->responseType;
        $urlBuilder[] = "scope=" . $this->scope;
        $urlBuilder[] = "state=" . $this->state;
        $urlBuilder[] = "redirect_uri=" . urlencode($this->redirectURL);
        return $this->dialog . "?" . implode("&", $urlBuilder);
    }

    protected function getToken() {
        $tokenBuilder = [];
        $tokenBuilder["client_id"] = $this->clientId;
        $tokenBuilder["client_secret"] = $this->client_secret;
        $tokenBuilder["grant_type"] = "authorization_code";
        $tokenBuilder["redirect_uri"] = htmlspecialchars($this->redirectURL);
        $tokenBuilder["code"] = $this->request["code"];
        $curl = new ccurl($this->accessToken);
        $curl->setPost($tokenBuilder);
        $curl->createCurl();
        return (string)$curl;
    }

    protected function parseToken() {
        $token = $this->getToken();
        $convertedToken = json_decode($token, true);
        if (!$convertedToken) {
            $realToken = $token;
        } else {
            $realToken = $convertedToken["access_token"];
        }

        return $realToken;
    }

    public function getProfile() {
        $token = $this->parseToken();

        $profileUrl = $this->profile . "=" . $token;
        $curl = new ccurl($profileUrl);
        $curl->addHeader($this->header . " " . $token);
        $curl->createCurl();
        $this->profileData = json_decode((string)$curl, true);
        return (string)$curl;
    }

    public function getEmail() {
        return null;
    }

    public function getFirstName() {
        return null;
    }

    public function getLastName() {
        return null;
    }

    public function getGender() {
        return null;
    }

    public function getId() {
        return null;
    }

    public function getRawProfile() {
        return $this->profileData;
    }

    public function getSource() {
        return null;
    }
}
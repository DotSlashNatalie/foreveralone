<?php

namespace system\vendor\phpoauthlib2;

class OAuth {
    protected $oauthProvider = null;
    protected $request = null;

    public function __construct($provider, $request) {
        $this->oauthProvider = $provider;
        $this->request = $request;
    }

    public function check() {
        if (isset($this->request["code"]) && !empty($this->request["code"])) {
            $this->oauthProvider->getProfile();
            return true;
        } else {
            return $this->oauthProvider->getLoginUrl();
        }
    }

    public function getProfile() {
        return $this->oauthProvider->getProfile();
    }
}
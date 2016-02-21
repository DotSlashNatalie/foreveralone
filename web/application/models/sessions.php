<?php

namespace application\models;

class Sessions extends \system\engine\HF_Model {
    public $sessionid;
    public $ip;
    public $userAgent;
    public $data;

    public function setData($key, $val) {
        $raw = json_decode($this->data, true);
        $raw[$key] = $val;
        $this->data = json_encode($raw);
    }

    public function getData($key) {
        $raw = json_decode($this->data, true);
        if (isset($raw[$key])) {
            return $raw[$key];
        } else {
            return null;
        }
    }
}
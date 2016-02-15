<?php

namespace application\models;

class Sessions extends \system\engine\HF_Model {
    public $sessionid;
    public $ip;
    public $userAgent;
    public $data;
}
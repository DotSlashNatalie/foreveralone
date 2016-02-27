<?php

use \application\models\Sessions;

abstract class base extends \system\engine\HF_Controller {

    /** @var  \application\models\Users $user */
    protected $user = null;
    /** @var \application\models\Sessions $session */
    protected $session = null;
    protected $sessionData = null;
    protected $loginRequired = true;
    protected $sessionRequired = true;
    protected function isLoggedIn() {
        if (!$this->sessionData && !isset($this->sessionData->userId)) {
            header("Location: /login");
            return false;
        } else {
            return true;
        }
    }

    protected function loadRender($template, $parameters=array()) {
        $newParameters = array_merge($parameters, ["session" => $this->sessionData, "config" => $this->config, "user" => $this->user]);
        return parent::loadRender($template, $newParameters);
    }

    protected function setupSession() {
        if (isset($_COOKIE["session"])) {
            $validSession = Sessions::getByField("sessionid", $_COOKIE["session"]);
            if ($validSession) {
                try {
                    $this->session = $validSession[0];
                    $this->sessionData = json_decode($this->session->data);
                } catch (\Exception $e) { }
            } else {
                $bytes = openssl_random_pseudo_bytes(10, $bool);
                $sessionId = bin2hex($bytes);
                $this->session = new Sessions();
                $this->session->ip = $_SERVER["REMOTE_ADDR"];
                $this->session->userAgent = $_SERVER["HTTP_USER_AGENT"];
                $this->session->sessionid = $sessionId;
                $this->session->save();
                setcookie("session", $sessionId, 2147483647, "/");
            }
        } else {
            $bytes = openssl_random_pseudo_bytes(10, $bool);
            $sessionId = bin2hex($bytes);
            $this->session = new Sessions();
            $this->session->ip = $_SERVER["REMOTE_ADDR"];
            $this->session->userAgent = $_SERVER["HTTP_USER_AGENT"];
            $this->session->sessionid = $sessionId;
            $this->session->id = $this->session->save();
            setcookie("session", $sessionId, 2147483647, "/");
        }
    }

    public function __construct($config, $core, $tpl)
    {
        parent::__construct($config, $core, $tpl);
        $core->setupDatabaseConnection();

        $expiredSessions = \vendor\DB\DB::fetchObject("SELECT * FROM sessions WHERE lastPing <= ?", "\application\models\Sessions", [time() - 20]);
        /** @var \application\models\Sessions $session */
        foreach($expiredSessions as $session) {
            if ($session->to_user) {
                /** @var \application\models\Sessions $otherSession */
                $otherSession = \application\models\Sessions::getByField("id", $session->to_user);
                $otherSession->waiting = true;
                $otherSession->to_user = null;
                $otherSession->save();
            }
            $session->delete();
        }

        $this->setupSession();
        if ($this->session) {
            $this->session->lastPing = time();
            $this->session->save();
        }

        if (isset($_POST["csrfmiddlewaretoken"])) {
            if ($_POST["csrfmiddlewaretoken"] != $_COOKIE["csrftoken"]) {
                throw new \Exception("CSRF tokens did not match");
            }
        }
    }
}
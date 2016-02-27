<?php

class main extends base
{
    public function index()
    {
        echo $this->loadRender("main.html");
    }

    public function info($page = null) {
        if (!$page) {
            echo $this->loadRender("info1.html");
        } else {
            if (is_numeric($page)) {
                echo $this->loadRender("info$page.html");
            } else {
                $notfound = new \system\engine\HF_Status($this->config, $this->core);
                echo $notfound->Status404();
            }
        }
    }

    public function chat() {
        $this->session->waiting = 1;
        $this->session->save();
        $toUser = $this->session->to_user;
        if ($toUser) {
            /** @var \application\models\Sessions $otherUserSession */
            $otherUserSession = \application\models\Sessions::getByField("id", $toUser);
            if ($otherUserSession) {
                $otherUserSession = $otherUserSession[0];
                $otherUserSession->waiting = true;
                $otherUserSession->to_user = null;
                $otherUserSession->save();
            }
            $this->session->to_user = null;
            $this->session->save();
        }

        echo $this->loadRender("chat.html");
    }

    public function match() {
        $result = false;

        echo json_encode($result);
    }

    public function sessionset($key) {
        if (in_array($key, ["interests", "gender", "looking"])) {
            $this->session->setData($key, $_POST[$key]);
            $this->session->save();
        }
    }

    public function togglerandom() {
        if ($_POST["random"] == "true") {
            $this->session->random = 1;
        } else {
            $this->session->random = 0;
        }
        $this->session->save();
    }

    public function send() {
        $message = new application\models\Messages();
        $message->user_from = $this->session->id;
        $message->user_to = $this->session->to_user;
        $message->message = $_POST["message"];
        $message->save();
    }

    public function read() {
        $result = false;
        $search = false;

        // work around for SQLite
        $lock = \application\models\Settings::getSetting("readLock");

        while($lock) {
            $lock = \application\models\Settings::getSetting("readLock");
        }
        \application\models\Settings::setSetting("readLock", (int)true);

        // Check if the current user is talking to someone
        $toUser = $this->session->to_user;
        /** @var \application\models\Sessions $session */
        $otherSession = \application\models\Sessions::getByField("id", $toUser);
        if ($otherSession) {
            $otherSession = $otherSession[0];
            // If they aren't waiting and the current toUser is this user..
            if ($otherSession->waiting && $toUser != $this->session->id) {
                $search = true;
            }
        } else {
            $search = true;
        }

        // search for someone else in waiting queue
        /** @var \application\models\Sessions $firstResult */
        $firstResult = null;
        if ($search) {
            $allSessions = \application\models\Sessions::getByField("waiting", 1);
            shuffle($allSessions);
            shuffle($allSessions);
            /** @var \application\models\Sessions $session */
            foreach ($allSessions as $session) {
                if ($session->getData("toUser") == $this->session->id && $toUser == null) {
                    // "kick the other user"
                    $session->to_user = null;
                    $session->waiting = 0;
                    $session->save();
                    continue;
                }
                if ($session->waiting && $session->id != $this->session->id) {
                    $firstResult = $session;
                    $interestWeight = [];
                    $gender1Weight = true;
                    $gender2Weight = true;
                    try {
                        $interestWeight = array_intersect($this->session->getData("interests"), $session->getData("interests"));
                        $gender1Weight = in_array($session->getData("gender"), $this->session->getData("looking"));
                        $gender2Weight = in_array($this->session->getData("gender"), $session->getData("looking"));
                    } catch (\Exception $e) { }
                    if ($gender1Weight && $gender2Weight && count($interestWeight) > 0) {
                        $result = true;
                        $session->waiting = 0;
                        $session->to_user = $this->session->id;
                        $this->session->to_user = $session->id;
                        $this->session->waiting = 0;
                        $session->save();
                        $this->session->save();
                        break;
                    }
                }
            }

            // If no match was made - match with first session
            if ($firstResult && !$result && $this->session->random) {
                $firstResult->waiting = 0;
                $firstResult->to_user = $this->session->id;
                $this->session->to_user = $firstResult->id;
                $this->session->waiting = 0;
                $firstResult->save();
                $this->session->save();
                $result = true;
            }

            if (!$result) {
                \application\models\Settings::setSetting("readLock", (int)false);
                echo json_encode(false);
                return;
            }
        }



        // return any messages waiting to be delivered
        $messages = \application\models\Messages::getByField("user_to", $this->session->id);
        $return = [];
        foreach($messages as $message) {
            $return[] = $message->user_from . ": " . $message->message;
            $message->delete();
        }

        echo json_encode($return);
        \application\models\Settings::setSetting("readLock", (int)false);
    }
}
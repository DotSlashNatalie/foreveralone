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
        $this->session->setData("waiting", true);
        $toUser = $this->session->getData("toUser");
        if ($toUser) {
            /** @var \application\models\Sessions $otherUserSession */
            $otherUserSession = \application\models\Sessions::getByField("id", $toUser);
            if ($otherUserSession) {
                $otherUserSession = $otherUserSession[0];
                $otherUserSession->setData("waiting", "true");
                $otherUserSession->setData("toUser", null);
                $otherUserSession->save();
            }
        }
        $this->session->setData("toUser", null);
        $this->session->save();
        echo $this->loadRender("chat.html");
    }

    public function match() {
        $result = false;

        echo json_encode($result);
    }

    public function sessionset($key) {
        if (in_array($key, ["interests", "gender", "looking"])) {
            $this->session->setData($key, $_POST[$key]);
        }
    }

    public function send() {
        $message = new application\models\Messages();
        $message->user_from = $this->session->id;
        $message->user_to = $this->session->getData("toUser");
        $message->message = $_POST["message"];
        $message->save();
    }

    public function read() {
        $result = false;
        $search = false;

        // work around for SQLite
        $lock = \application\models\Settings::getSetting("readLock");

        while($lock) {
            $lock = false;
        }
        \application\models\Settings::setSetting("readLock", (int)true);

        // Check if the current user is talking to someone
        $toUser = $this->session->getData("toUser");
        /** @var \application\models\Sessions $session */
        $otherSession = \application\models\Sessions::getByField("id", $this->session->getData("toUser"));
        if ($otherSession) {
            $otherSession = $otherSession[0];
            // If they aren't waiting and the current toUser is this user..
            if ($otherSession->getData("waiting") && $otherSession->getData("toUser") != $this->session->id) {
                $search = true;
            }
        } else {
            $search = true;
        }

        // search for someone else in waiting queue
        /** @var \application\models\Sessions $firstResult */
        $firstResult = null;
        if ($search) {
            $allSessions = \application\models\Sessions::all();
            shuffle($allSessions);
            shuffle($allSessions);
            /** @var \application\models\Sessions $session */
            foreach ($allSessions as $session) {
                if ($session->getData("toUser") == $this->session->id && $this->session->getData("toUser") == null) {
                    // "kick the other user"
                    $session->setData("toUser", null);
                    $session->setData("waiting", false);
                    continue;
                }
                if ($session->getData("waiting") && $session->id != $this->session->id) {
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
                        $session->setData("waiting", false);
                        $session->setData("toUser", $this->session->id);
                        $this->session->setData("toUser", $session->id);
                        $this->session->setData("waiting", false);
                        $session->save();
                        $this->session->save();
                        break;
                    }
                }
            }

            // If no match was made - match with first session
            if ($firstResult && !$result) {
                $firstResult->setData("waiting", false);
                $firstResult->setData("toUser", $this->session->id);
                $this->session->setData("toUser", $firstResult->id);
                $this->session->setData("waiting", false);
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
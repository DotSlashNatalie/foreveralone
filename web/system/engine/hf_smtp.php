<?php

namespace system\engine;

// Based on tutorial from here: https://portal.cyberhostpro.com/knowledgebase/170/PHP-Mail-Script-with-SMTP-Authentication.html
class HF_SMTP
{

    private $from = "";
    private $to = "";
    private $subject = "";
    private $msg = "";
    private $user = null;
    private $password = null;
    private $port = 25;
    private $server = "localhost";

    private $smtpserverconn = null;

    public function __construct($from, $to, $subject, $msg, $server = "localhost", $user = null, $password = null, $port = 25)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->msg = $msg;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->server = $server;
    }

    public function send($html=false)
    {
        $err = null;
        $errstr = "";
        $this->smtpserverconn = fsockopen($this->server, $this->port, $err, $errstr, 100);
        $response = fgets($this->smtpserverconn, 515);
        if ($response === false)
        {
            throw new Exception("Could not connect to SMTP server!");
        }

        if ($this->user != null && $this->password != null)
        {
            $this->sendCommand("AUTH LOGIN");
            $this->sendCommand(base64_encode($this->user));
            $this->sendCommand(base64_encode($this->password));
        }

        $this->sendCommand("HELO " . $_SERVER["SERVER_NAME"]);
        $this->sendCommand("MAIL FROM: " . $this->from);
        $this->sendCommand("RCPT TO: " . $this->to);
        $this->sendCommand("DATA");

        if ($html)
        {
            $this->sendCommand("MIME-Version: 1.0", false);
            $this->sendCommand("Content-type: text/html; charset=iso-8859-1", false);
        }

        $this->sendCommand("From: " . $this->from, false);
        $this->sendCommand("To: " . $this->to, false);
        $this->sendCommand("Subject: " . $this->subject, false);


        $this->sendCommand($this->msg, false);

        $this->sendCommand("", false);
        $this->sendCommand(".", false);
        $this->sendCommand("QUIT");

    }

    private function sendCommand($command, $return = true)
    {
        fputs($this->smtpserverconn, $command . "\r\n");
        if ($return)
            return fgets($this->smtpserverconn, 515);
        else
            return null;
    }
}
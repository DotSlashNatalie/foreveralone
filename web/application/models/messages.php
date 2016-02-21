<?php

namespace application\models;

use system\engine\HF_Model;

class Messages extends HF_Model
{
    public $user_to;
    public $user_from;
    public $message;
}
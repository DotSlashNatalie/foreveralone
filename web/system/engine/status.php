<?php

namespace system\engine;

class HF_Status extends HF_Controller
{

    public function Status404()
    {
        echo "Page not found!";
    }

    public function Status500()
    {
        echo "System error";
    }
}
<?php

spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register();

foreach (glob("system/vendor/*.php") as $filename)
{
    include $filename;
}

$core = new \system\engine\HF_Core();
$core->run();
<?php

function isvarset(&$var)
{
    return (isset($var) && (!empty($var) || is_numeric($var))) ? true : false;
}
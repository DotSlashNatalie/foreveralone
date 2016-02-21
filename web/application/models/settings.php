<?php

namespace application\models;

use system\engine\HF_Model;

class Settings extends HF_Model
{
    public $setting;
    public $value;

    public static function getSetting($key) {
        $setting = \application\models\Settings::getByField("setting", $key);
        if ($setting) {
            return $setting[0]->value;
        } else {
            return null;
        }
    }

    public static function setSetting($key, $val) {
        $setting = \application\models\Settings::getByField("setting", $key);
        if (!$setting) {
            $setting = new \application\models\Settings();
            $setting->setting = $key;
        } else {
            $setting = $setting[0];
        }
        $setting->value = $val;
        $setting->save();
    }
}
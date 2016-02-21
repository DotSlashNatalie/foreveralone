<?php

use \vendor\DB\DB;

DB::query("CREATE TABLE settings
(
    id INTEGER PRIMARY KEY,
    setting TEXT,
    value TEXT
);");
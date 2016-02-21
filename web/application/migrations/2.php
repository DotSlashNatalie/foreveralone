<?php

use \vendor\DB\DB;

$autoIncrement = \system\engine\HF_Model::AUTOINCREMENT_SQLITE;
DB::query("CREATE TABLE messages (
          id INTEGER PRIMARY KEY $autoIncrement,
          user_to INTEGER,
          user_from INTEGER,
          message VARCHAR(255)
)");
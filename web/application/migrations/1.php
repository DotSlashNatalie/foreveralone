<?php

use \vendor\DB\DB;

echo "Creating session table..." . PHP_EOL;
$autoIncrement = \system\engine\HF_Model::AUTOINCREMENT_SQLITE;

DB::query("CREATE TABLE sessions (
          id INTEGER PRIMARY KEY $autoIncrement,
          sessionid VARCHAR(255),
          ip VARCHAR(255),
          userAgent VARCHAR(255),
          data TEXT,
          waiting TINYINT(1),
          to_user INTEGER,
          lastPing INTEGER,
          random TINYINT(1)
)");

DB::query("CREATE INDEX waiting_idx ON sessions(waiting)");
DB::query("CREATE INDEX lastping_idx ON sessions(lastPing)");

DB::query("CREATE TABLE sessionlogs (
          id INTEGER PRIMARY KEY $autoIncrement,
          original_id INTEGER,
          sessionid VARCHAR(255),
          ip VARCHAR(255),
          userAgent VARCHAR(255)
)");
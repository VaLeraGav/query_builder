<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lacerta\DB;

$db = DB::setup();
$db->exec(
    "CREATE TABLE users (
            id INTEGER not null primary key autoincrement,
            name VARCHAR(255),
            number VARCHAR(255),
            email EMAIL,
            password VARCHAR(255),
            created_at TIMESTAMP
        )"
);

$tabl = $db->query(
    "SELECT * FROM users"
);

print_r($tabl);
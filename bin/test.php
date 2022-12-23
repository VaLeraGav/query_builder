<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lacerta\DB;

$db = DB::setup()->table("users")->query("SELECT * FROM users", null);

print_r($db);
//$table1 = $db->select("name_field");

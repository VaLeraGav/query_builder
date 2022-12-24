<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lacerta\Builder;
use Lacerta\Connect;

//$db = Connect::setup()->table("users")->query("SELECT * FROM users", null)->fetch();
// $db = Connect::setup()->table("users")->select();
$conect = Connect::setup();

$db = $conect->table('users')->insert([
    [
        "name" => "name",
        "age" => 12,
        "email" => "vaa@email.tr"
    ],
    [
        "age" => 12,
        "name" => "name12",
        "email" => "qqvaa@email.tr"
    ]
])->getStr();
//$db = $conect->table('users')->insert()->values(["av", "as", "a"])->fields(["name", "age", "email"]);
print_r($db);
//$db = $conect->insert()
//    ->fields('name', 'email', 'age')
//    ->values('Vasya', 'vasya@gmail.com', 22)
//    ->values('Petya', 'petya@gmail.com', 24);


//$db = Connect::setup()->table("users")->
//print_r($db);
//$table1 = $db->select("name_field");


//Connect::setup()->exec(
//    "CREATE TABLE users (
//       id INTEGER not null primary key autoincrement,
//       name VARCHAR(255),
//       email VARCHAR(255),
//       age INT
//    )"
//);


//Connect::setup()->exec(
//    "CREATE TABLE users (
//       id INTEGER not null primary key autoincrement,
//       name VARCHAR(255),
//       email VARCHAR(255),
//       age VARCHAR(255),
//       created_at TIMESTAMP
//    )"
//);

<?php

// Future package mapping example.

$packagePath = __DIR__;

return [
  "name" => "User",
  "directories" => [
    "Controllers" => [
      "$packagePath/Controllers/User.php"
    ],
    "Migrations" => [
      "$packagePath/Migrations/up.sql",
      "$packagePath/Migrations/down.sql"
    ],
    "Models" => [
      "$packagePath/Models/UserModel.php",
    ],
    "Views" => [
      "$packagePath/Views/login.tpl"
    ]
  ]
];

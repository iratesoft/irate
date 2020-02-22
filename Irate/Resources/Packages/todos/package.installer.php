<?php

// Future package mapping example.

$packagePath = __DIR__;

return [
  "name" => "Todos",
  "directories" => [
    "Controllers" => [
      "$packagePath/Controllers/Todos.php"
    ],
    "Migrations" => [
      "$packagePath/Migrations/up.sql",
      "$packagePath/Migrations/down.sql"
    ],
    "Models" => [
      "$packagePath/Models/TodoModel.php",
    ],
    "Views" => [
      "$packagePath/Views/index.tpl"
    ]
  ],
  "assets" => [
    'scripts' => [
      "$packagePath/assets/scripts/todo.class.js"
    ]
  ],
  "routes" => [
    "['route' => 'todos', 'params' => ['controller' => 'Todos', 'action' => 'index']],",
    "['route' => 'todos/update/{id:\d+}', 'params' => ['controller' => 'Todos', 'action' => 'update']],",
    "['route' => 'todos/remove/{id:\d+}', 'params' => ['controller' => 'Todos', 'action' => 'remove']],"
  ]
];

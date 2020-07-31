<?php

/**
 * You can
 */

$config = [
  /**
   * Base URL of Application
   */
  'BASE_URL' => isset($_ENV['baseUrl']) ? $_ENV['baseUrl'] : '',

  /**
   * Database Information
   */
  'DB_HOST'     => isset($_ENV['DB_HOST'])     ? $_ENV['DB_HOST']     : '',
  'DB_NAME'     => isset($_ENV['DB_NAME'])     ? $_ENV['DB_NAME']     : '',
  'DB_USER'     => isset($_ENV['DB_USER'])     ? $_ENV['DB_USER']     : '',
  'DB_PASSWORD' => isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '',
  'DB_CHARSET'  => isset($_ENV['DB_CHARSET'])  ? $_ENV['DB_CHARSET']  : 'utf8mb4',

  /**
   * Show dev errors
   */
  'SHOW_ERRORS' => true,

  /**
   * Router Defaults
   */
  'ROUTE_DEFAULT_CONTROLLER' => 'Site',
  'ROUTE_DEFAULT_ACTION'     => 'index',

  /**
   * Defined Routes
   */
  'ROUTES' => [
    // [
    //   'route'  => 'example/{id}',
    //   'params' => [
    //     'controller' => 'Example',
    //     'action'     => 'view'
    //   ]
    // ]
  ],

  /**
   * List of libraries that you would like
   * to be preloaded into IrateFramework.
   *
   * These libraries are accessed via the following:
   * Controllers, Models, Views
   *
   * With the following syntax:
   * $libraries->test->test(); // For Views
   * $this->libraries->test->test(); // For Controllers and Models
   */
  'PRELOADED_LIBRARIES' => [
    '\\Application\\Libraries\\Test'
  ],

  /**
   * Encoding key for things like sessions
   */
  'ENCODING_KEY' => 'UNIQUE_KEY_HERE',

  /**
   * SMTP Configuration
   */
  'SMTP_HOST'     => '',
  'SMTP_USERNAME' => '',
  'SMTP_PASSWORD' => '',
  'SMTP_PORT'     => 587,

  /**
   * Application parameters
   */
  'PARAMS' => [
    'siteTitle' => 'IrateFramework'
  ],
];

return (object) $config;

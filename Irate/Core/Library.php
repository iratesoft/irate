<?php

namespace Irate\Core;

use \PDO;
use Irate\System;

/**
 * Base model
 */
abstract class Library
{

  // Instance of the request class
  protected $request;

  // Instance of the security class
  protected $security;

  // Instance of the db class
  protected $db;

  // Instance of the email class
  protected $email;

  // Instance of the session class
  protected $session;

  public function __construct() {

    // Set all class instances from System
    $this->db       = System::$db;
    $this->request  = System::$request;
    $this->security = System::$security;
    $this->email    = System::$email;
    $this->session  = System::$session;

    $this->instantiate();
  }

  /**
   * Function that will run immediately after construct
   * so the actual model class doesn't need to run the constructor
   * and mess with the class var settings.
   */
  public function instantiate() {

  }
}

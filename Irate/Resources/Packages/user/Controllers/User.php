<?php

namespace Application\Controllers;

use Irate\System;
use Irate\Core\Controller;
use Application\Models\UserModel;

/**
 * User controller
 */
class User extends Controller {

  public function login() {
    System::$view->renderTemplate('user/login');
  }
}

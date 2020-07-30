<?php

namespace Application\Controllers;

/**
 * Site controller
 */
class Site extends \Irate\Core\Controller {

  /**
   * Application site index
   */
  protected function index() {
    return $this->view->renderTemplate('site/index');
  }

  protected function test() {

  }

}

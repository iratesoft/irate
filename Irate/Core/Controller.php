<?php

namespace Irate\Core;

abstract class Controller {
  /**
  * Parameters from the matched route
  * @var array
  */
  protected $params = [];

  protected $view;
  protected $request;
  protected $response;
  protected $security;
  protected $session;

  /**
  * Class constructor
  */
  public function __construct($system, $params)
  {
    $this->view     = $system::$view;
    $this->request  = $system::$request;
    $this->response = $system::$response;
    $this->security = $system::$security;
    $this->session  = $system::$session;
    $this->params   = $params;
  }

  public function __call($name, $args) {
    $method = $name;

    if (method_exists($this, $method)) {
      if ($this->before() !== false) {
        call_user_func_array([$this, $method], $args);
        $this->after();
      }
    } else {
      throw new \Exception("Method $method not found in controller " . get_class($this));
    }
  }

  /**
  * Before filter - called before an action method.
  *
  * @return void
  */
  protected function before() {

  }

  /**
  * After filter - called after an action method.
  *
  * @return void
  */
  protected function after() {

  }

  protected function redirect($url = null) {
    if (is_null($url)) throw new \Exception('You must provide a URL to redirect to. ' . get_class($this));
    return header("Location: " . $url);
  }
}

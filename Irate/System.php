<?php

namespace Irate;

// Multiple class uses
use Irate\Core\Router;
use Irate\Core\Logger;
use Irate\Core\Request;
use Irate\Core\Response;
use Irate\Core\View;
use Irate\Core\Connection;
use Irate\Core\AssetBundle;
use Irate\Core\Security;
use Irate\Core\Session;
use Irate\Core\Email;

// Define globals if not already.
defined('IRATE_PATH')     or define('IRATE_PATH',  __DIR__);
defined('IRATE_ENV')      or define('IRATE_ENV',   'dev');
defined('IRATE_DEBUG')    or define('IRATE_DEBUG', false);
defined('PHPMAILER_PATH') or define('PHPMAILER_PATH', IRATE_PATH . "/../vendor/phpmailer/phpmailer/src/");

// Require PHPMailer
require PHPMAILER_PATH . "PHPMailer.php";
require PHPMAILER_PATH . "SMTP.php";
require PHPMAILER_PATH . "Exception.php";

// Set error & exception handlers
set_error_handler('Irate\Core\Error::errorHandler');
set_exception_handler('Irate\Core\Error::exceptionHandler');

class System {

  public static $version = 'Irate Framework v0.0.1-4 RC';

  public $config;
  public $baseUrl = '/';

  // Set the Irate Router as a variable.
  protected $router;

  // Publicly accessible resources
  public static $request;
  public static $view;
  public static $params = [];
  public static $db;
  public static $response;
  public static $AssetBundle = false;
  public static $security;
  public static $session;
  public static $email;

  public function __construct() {
    // Set config and params
    $this->setConfig();
    $this->setParams();

    // Begin instantiating the classes.
    $this->instantiate();
  }

  public function run() {

    // Instantiate the router with the routes from the application.
    // TODO: Do a routes check, make sure it exists.
    $this->router = new Router($this);

    // Dispatch the router (Run it)
    $this->router->run($_SERVER['QUERY_STRING']);
  }

  /**
   * Instantiates resource classes that can be
   * used throughout
   */
  private function instantiate() {
    self::$request     = new Request();
    self::$view        = new View(['system' => $this, 'baseUrl' => $this->getBaseUrl()]);
    self::$db          = new Connection();
    self::$AssetBundle = new AssetBundle(['baseUrl' => $this->getBaseUrl()]);
    self::$response    = new Response();
    self::$email       = new Email(['config' => $this->config, 'view' => self::$view]);

    // Certain classes can not instantiate on CLI
    if (!self::isCLI()) {
      self::$security = new Security(['config' => $this->config]);
      self::$session  = new Session();
    }
  }

  /**
   * Makes the Application\Config class accessible from
   * the system itself. (System->config::PARAMS for example)
   */
  private function setConfig() {
    if (!class_exists('\Application\Config'))
      throw new \Exception('\Application\Config does not exist.');

    $this->config = new \Application\Config;
  }

  /**
   * Sets parameters for system.
   */
  private function setParams() {
    if (\Application\Config::PARAMS) {
      self::$params = \Application\Config::PARAMS;
    }
  }

  /**
   * Return all or one parameter.
   * Irate\System::param('key')
   */
  public static function param($key = null) {
    if (is_null($key)) return self::$params;
    if (isset(self::$params[$key])) return self::$params[$key];
    return false;
  }

  public function getBaseUrl() {
    if ($this->config::BASE_URL) {
      return $this->config::BASE_URL;
    } else {
      return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    }
  }

  public static function isCLI() {
    if (php_sapi_name() === 'cli') return true;
    return false;
  }
}

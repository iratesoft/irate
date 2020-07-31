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

// Root path must be defined to load the .env
if (defined('ROOT_PATH')) {
  $dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
  $dotenv->load();
}

// Define globals if not already.
defined('IRATE_PATH')        or define('IRATE_PATH',  __DIR__);
defined('IRATE_ENV')         or define('IRATE_ENV', (isset($_ENV['env']) ? $_ENV['env'] : 'dev'));
defined('IRATE_DEBUG')       or define('IRATE_DEBUG', (isset($_ENV['debug']) ? $_ENV['debug'] : false));
defined('IRATE_PUBLIC_PATH') or define('IRATE_PUBLIC_PATH', IRATE_PATH . '/../public');

class System {

  public static $version = 'Irate Framework v0.0.1-5 RC';

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

  public function __construct($config = []) {
    // Set config and params
    $this->setConfig();
    $this->setParams();

    // If configuration states NOT to run instance with a
    // database connection.
    $dbInstantiation = true;
    if (isset($config['db'])) {
      $dbInstantiation = $config['db'];
    }

    // Begin instantiating the classes.
    $this->instantiate($dbInstantiation);
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
  private function instantiate($dbInstantiation) {
    defined('IRATE_SHOW_ERRORS') or define('IRATE_SHOW_ERRORS', (
      isset($this->config->SHOW_ERRORS) ?
      $this->config->SHOW_ERRORS :
      false
    ));

    defined('IRATE_LOG_PATH') or define('IRATE_LOG_PATH', (
      isset($this->config->LOG_PATH) ?
      $this->config->LOG_PATH :
      ROOT_PATH . '/logs/'
    ));

    // Set error & exception handlers
    set_error_handler('Irate\Core\Error::errorHandler');
    set_exception_handler('Irate\Core\Error::exceptionHandler');

    self::$request     = new Request(['config' => $this->config]);
    if ($dbInstantiation) self::$db = new Connection(['config' => $this->config]);
    self::$response    = new Response(['config' => $this->config]);
    self::$email       = new Email(['config' => $this->config, 'view' => self::$view]);

    // Certain classes can not instantiate on CLI
    if (!self::isCLI()) {
      self::$AssetBundle = new AssetBundle(['baseUrl' => $this->getBaseUrl(), 'config' => $this->config]);
      self::$security    = new Security(['config' => $this->config]);
      self::$session     = new Session(['config' => $this->config]);
      self::$view        = new View(['system' => $this, 'baseUrl' => $this->getBaseUrl()]);
    }
  }

  /**
   * Makes the Application\Config class accessible from
   * the system itself. (System->config::PARAMS for example)
   */
  private function setConfig() {
    if (!file_exists(ROOT_PATH . '/Application/Config.php'))
      throw new \Exception('/Application/Config.php does not exist.');

    $config = require ROOT_PATH . '/Application/Config.php';

    if (isset($config->ENCODING_KEY)) {
      if (IRATE_ENV === 'production' && $config->ENCODING_KEY === 'UNIQUE_KEY_HERE') {
        throw new \Exception('Please update your encoding key to something unique.');
      }
    }

    $this->config = $config;
  }

  /**
   * Sets parameters for system.
   */
  private function setParams() {
    if (isset($this->config->PARAMS)) {
      self::$params = $this->config->PARAMS;
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
    if ($this->config->BASE_URL) {
      return $this->config->BASE_URL;
    } else {
      return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    }
  }

  public static function isCLI() {
    if (php_sapi_name() === 'cli') return true;
    return false;
  }
}

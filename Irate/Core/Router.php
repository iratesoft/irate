<?php

namespace Irate\Core;

/**
 * Router
 *
 * PHP version 7.0
 */
class Router
{

    protected $routes = [];
    protected $params = [];

    private $system = false;

    /**
     * Set the system variable,
     * add routes from config and then any
     * default routes.
     */
    public function __construct($system) {
      $this->system = $system;

      // Add routes from config
      if (\Application\Config::ROUTES) {
        $this->addRoutes(\Application\Config::ROUTES);
      }

      // Adding the default route by default.
      $this->addRoutes([
        [ 'route' => '{controller}/{action}' ]
      ]);
    }

    public function addRoutes($routes) {
      foreach ($routes as $route) {
        $params = (isset($route['params']) ? $route['params'] : []);
        $this->add($route['route'], $params);
      }
    }

    public function add($route, $params = []) {
      $route = preg_replace('/\//', '\\/', $route);
      $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
      $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
      $route = '/^' . $route . '$/i';
      $this->routes[$route] = $params;
    }

    public function getRoutes() {
      return $this->routes;
    }

    public function match($url) {

      // If no route, then activate defaults.
      if (empty($url)) {
        $this->params = [
          'controller' => (
            \Application\Config::ROUTE_DEFAULT_CONTROLLER ?
            \Application\Config::ROUTE_DEFAULT_CONTROLLER :
            'Site'
          ),
          'action' => (
            \Application\Config::ROUTE_DEFAULT_ACTION ?
            \Application\Config::ROUTE_DEFAULT_ACTION :
            'index'
          )
        ];
        return true;
      }

      foreach ($this->routes as $route => $params) {
        if (preg_match($route, $url, $matches)) {
          // Get named capture group values
          foreach ($matches as $key => $match) {
            if (is_string($key)) {
                $params[$key] = $match;
            }
          }

          $this->params = $params;
          return true;
        }
      }

      // If the route has no slash, assume it's a controller with the index action.
      if (strpos('/', $url) === false) {
        $this->params = [
          'controller' => $this->convertToStudlyCaps($url),
          'action' => 'index'
        ];
        return true;
      }

      return false;
    }

    public function getParams() {
      return $this->params;
    }

    public function run($url) {
      $url = $this->removeQueryStringVariables($url);

      if ($this->match($url)) {
        $controller = $this->params['controller'];
        $controller = $this->convertToStudlyCaps($controller);
        $controller = $this->getNamespace() . $controller;

        if (class_exists($controller)) {
          $controller_object = new $controller($this->system, $this->params);

          $action = $this->params['action'];
          $action = $this->convertToCamelCase($action);

          if (preg_match('/action$/i', $action) == 0) {
            $controller_object->$action();
          } else {
            throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
          }
        } else {
          throw new \Exception("Controller class $controller not found");
        }
      } else {
        throw new \Exception('No route matched. ', 404);
      }
    }

    protected function convertToStudlyCaps($string) {
      return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string) {
      return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url) {
      if ($url != '') {
        $parts = explode('&', $url, 2);
        if (strpos($parts[0], '=') === false) $url = $parts[0];
        else $url = '';
      }
      return $url;
    }

    protected function getNamespace() {
      $namespace = 'Application\Controllers\\';
      if (array_key_exists('namespace', $this->params)) $namespace .= $this->params['namespace'] . '\\';
      return $namespace;
    }
}

<?php

namespace Irate\Core;

use \MatthiasMullie\Minify;

class AssetBundle {

  // Bundle informatino
  private $bundle = false;
  private $bundleName    = '\\Application\\Assets\\DefaultAssetBundle';
  private $bundleScripts = false;
  private $bundleStyles  = false;

  // Assets
  public static $SCRIPTS = [];
  public static $STYLES  = [];
  public static $BUNDLE_SCRIPTS = false;
  public static $BUNDLE_STYLES  = false;

  // Cache busting (false by default)
  public static $CACHE_BUST = false;

  // Base URL of application
  private static $baseUrl = false;
  private static $config = false;

  // Class constructor
  public function __construct($vars = []) {

    // Set the base URL if it's passed.
    if (isset($vars['baseUrl'])) {
      self::$baseUrl = $vars['baseUrl'];
    }

    if (isset($vars['config'])) {
      self::$config = $vars['config'];
    }

    if (isset($_ENV['baseUrl'])) self::$baseUrl = $_ENV['baseUrl'];

    $this->setBundle();
  }

  /**
   * @return string Script tags based on scripts in AssetBundle
   */
  public function scripts() {
    $res = "";

    /**
     * Iterate through each URL, validate the URL, if it's relative,
     * prepend the Application BASE_URL if provided.
     */
    foreach (self::$SCRIPTS as $script) {

      if (strpos($script, $_SERVER['HTTP_HOST']) === false &&
          strpos($script, 'http://') === false             &&
          strpos($script, 'https://') === false) {
        $script = (
          substr(self::$baseUrl, -1) === '/' ?
          self::$baseUrl . (substr($script, 1) === '/' ? substr($script, 1) : $script) :
          self::$baseUrl . '/' . ($script[0] === '/' ? substr($script, 1) : $script)
        );
      }

      $res .= "<script src=\"" . $script . (self::$CACHE_BUST ? '?v=' . time() : '') . "\"></script>\r\n";
    }

    if ($this::$BUNDLE_SCRIPTS && IRATE_ENV === 'production') {

      $generatedFileRelativePath = '/generated/app.bundle.js';
      $generatedFileAbsolutePath = IRATE_PUBLIC_PATH . $generatedFileRelativePath;
      $fileExists = file_exists($generatedFileAbsolutePath);
      $buildFile = false;

      if (!$fileExists) $buildFile = true;

      if ($buildFile === true) {
        $minifier = new Minify\JS();

        foreach (self::$SCRIPTS as $script) {
          $minifier->add(IRATE_PUBLIC_PATH . $script);
        }

        file_put_contents($generatedFileAbsolutePath, $minifier->minify());
      }

      $generatedUrl = (
        substr(self::$baseUrl, -1) === '/' ?
        self::$baseUrl . (substr($generatedFileRelativePath, 1) === '/' ? substr($generatedFileRelativePath, 1) : $generatedFileRelativePath) :
        self::$baseUrl . '/' . ($generatedFileRelativePath[0] === '/' ? substr($generatedFileRelativePath, 1) : $generatedFileRelativePath)
      );

      return "<script src=\"" . $generatedUrl . (self::$CACHE_BUST ? '?v=' . time() : '') . "\"></script>\r\n";
    }

    return $res;
  }

  /**
   * @return string Style tags based on styles in AssetBundle
   */
  public function styles() {
    $res = "";

    /**
     * Iterate through each URL, validate the URL, if it's relative,
     * prepend the Application BASE_URL if provided.
     */
    foreach (self::$STYLES as $style) {
      if (strpos($style, $_SERVER['HTTP_HOST']) === false &&
          strpos($style, 'http://') === false           &&
          strpos($style, 'https://') === false) {
        $style = (
          substr(self::$baseUrl, -1) === '/' ?
          self::$baseUrl . (substr($style, 1) === '/' ? substr($style, 1) : $style) :
          self::$baseUrl . '/' . ($style[0] === '/' ? substr($style, 1) : $style)
        );
      }

      $res .= "<link href=\"" . $style . (self::$CACHE_BUST ? '?v=' . time() : '') . "\" rel=\"stylesheet\">\r\n";
    }

    if ($this::$BUNDLE_STYLES && IRATE_ENV === 'production') {

      $generatedFileRelativePath = '/generated/app.bundle.css';
      $generatedFileAbsolutePath = IRATE_PUBLIC_PATH . $generatedFileRelativePath;
      $fileExists = file_exists($generatedFileAbsolutePath);
      $buildFile = false;

      if (!$fileExists) $buildFile = true;

      if ($buildFile === true) {
        $minifier = new Minify\CSS();

        foreach (self::$STYLES as $style) {
          $minifier->add(IRATE_PUBLIC_PATH . $style);
        }

        file_put_contents($generatedFileAbsolutePath, $minifier->minify());
      }

      $generatedUrl = (
        substr(self::$baseUrl, -1) === '/' ?
        self::$baseUrl . (substr($generatedFileRelativePath, 1) === '/' ? substr($generatedFileRelativePath, 1) : $generatedFileRelativePath) :
        self::$baseUrl . '/' . ($generatedFileRelativePath[0] === '/' ? substr($generatedFileRelativePath, 1) : $generatedFileRelativePath)
      );

      return "<link href=\"" . $generatedUrl . (self::$CACHE_BUST ? '?v=' . time() : '') . "\" rel=\"stylesheet\">\r\n";
    }

    return $res;
  }

  /**
   * Sets the AssetBundle for the application. Sets DefaultAssetBundle
   * by default if it exists.
   * @param string $bundleName (Needs to match AssetBundle Classname)
   */
  public function setBundle($bundleName = null) {
    if (is_null($bundleName)) {
      if (class_exists('\Application\Assets\DefaultAssetBundle')) {
        $this->bundle = new \Application\Assets\DefaultAssetBundle;
      }
    } else {
      $bundleClassName = '\Application\Assets\\' . $bundleName;
      if (class_exists($bundleClassName)) {
        $this->bundle = new $bundleClassName;
        $this->bundleName = $bundleName;
      }
    }

    $this->setBundleVars();
  }

  /**
   * Sets necessary variables for the class from
   * the AssetBundle.
   */
  private function setBundleVars() {
    if ($this->bundle !== false) {
      if (defined($this->bundleName . "::SCRIPTS")) {
        if ($this->bundle::SCRIPTS) self::$SCRIPTS = $this->bundle::SCRIPTS;
        else self::$SCRIPTS = [];
      } else {
        self::$SCRIPTS = [];
      }

      if (defined($this->bundleName . "::STYLES")) {
        if ($this->bundle::STYLES) self::$STYLES = $this->bundle::STYLES;
        else self::$STYLES = [];
      } else {
        self::$STYLES = [];
      }

      if (defined($this->bundleName . "::CACHE_BUST")) {
        if ($this->bundle::CACHE_BUST) self::$CACHE_BUST = $this->bundle::CACHE_BUST;
        else self::$CACHE_BUST = false;
      } else {
        self::$CACHE_BUST = false;
      }

      if (defined($this->bundleName . "::BUNDLE_SCRIPTS")) {
        if ($this->bundle::BUNDLE_SCRIPTS) self::$BUNDLE_SCRIPTS = $this->bundle::BUNDLE_SCRIPTS;
        else self::$BUNDLE_SCRIPTS = false;
      } else {
        self::$BUNDLE_SCRIPTS = false;
      }

      if (defined($this->bundleName . "::BUNDLE_STYLES")) {
        if ($this->bundle::BUNDLE_STYLES) self::$BUNDLE_STYLES = $this->bundle::BUNDLE_STYLES;
        else self::$BUNDLE_STYLES = false;
      } else {
        self::$BUNDLE_STYLES = false;
      }
    }
  }
}

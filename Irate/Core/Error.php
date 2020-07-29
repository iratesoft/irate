<?php

namespace Irate\Core;

/**
 * Error and exception handler
 */
class Error
{

  public static function errorHandler($level, $message, $file, $line) {
    if (error_reporting() !== 0) throw new \ErrorException($message, 0, $level, $file, $line);
  }

  public static function exceptionHandler($exception) {
    /**
     * Configuration for showing errors.
     * If \Application\Config::SHOW_ERRORS is set,
     * we will base it off of that setting. If not,
     * set it to false by default.
     */
    $showErrors = false;
    if (IRATE_SHOW_ERRORS === true) {
      $showErrors = true;
    }

    /**
     * Configuration for log path
     * If \Application\Config::LOG_PATH is set,
     * we will base it off of that setting. If not,
     * set it to the below by default.
     */
    $logPath = ROOT_PATH . '/Logs/';
    if (defined("IRATE_LOG_PATH")) {
      $logPath = IRATE_LOG_PATH;
    }

    // Get the exception code
    $code = $exception->getCode();

    // Always set to 500 if not 404.
    if ($code != 404) $code = 500;
    http_response_code($code);

    /**
     * If show errors is true, display the details
     * of the error on the page.
     */
    if ($showErrors) {
        echo "<h1>Fatal error</h1>";
        echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        echo "<p>Message: '"        . $exception->getMessage() . "'</p>";
        echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
        echo "<p>Thrown in '"       . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
    } else {
        // Set the error log path.
        ini_set('error_log', $logPath . date('Y-m-d') . '.txt');

        // Output detailed error to the log.
        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '"   . $exception->getFile() . "' on line " . $exception->getLine();
        error_log($message);

        // Log the template for the specific error.
        // View::renderTemplate("errors/" . $code);
    }
  }
}

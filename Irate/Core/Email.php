<?php

namespace Irate\Core;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;

class Email {

  private $config;
  private $view;
  private $mail;

  public function __construct($data) {
    if (isset($data['config'])) $this->config = $data['config'];
    if (isset($data['view']))   $this->view   = $data['view'];

    // Instantiate PHPMailer
    $this->mail = new PHPMailer(true);

    // Check if SMTP setings are provided.
    if ($this->config::SMTP_HOST && !empty($this->config::SMTP_HOST)) {
      $this->mail->isSMTP();
      $this->mail->Host       = $this->config::SMTP_HOST;
      $this->mail->SMTPAuth   = true;
      $this->mail->Username   = $this->config::SMTP_USERNAME;
      $this->mail->Password   = $this->config::SMTP_PASSWORD;
      $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $this->mail->Port       = $this->config::SMTP_PORT;
    }
  }

  /**
   * Clears PHPMailer settings to setup
   * new emails.
   */
  public function clear() {
    $this->from = NULL;
    $this->mail->Subject = NULL;
    $this->mail->Body = NULL;
    $this->mail->ClearAddresses();  // each AddAddress add to list
    $this->mail->clearReplyTos();
    $this->mail->ClearCCs();
    $this->mail->ClearBCCs();
  }

  public function from($email, $name = '') {
    $this->mail->setFrom($email, $name);
    return $this;
  }

  public function to($email, $name = '') {
    $this->mail->addAddress($email, $name);
    return $this;
  }

  public function replyTo($email, $name = '') {
    $this->mail->addReplyTo($email, $name);
    return $this;
  }

  public function cc($emails = []) {
    foreach ($emails as $email) {
      $this->mail->addCC($email);
    }
    return $this;
  }

  public function bcc($emails = []) {
    foreach ($emails as $email) {
      $this->mail->addBCC($email);
    }
    return $this;
  }

  public function subject($subject) {
    $this->mail->Subject = $subject;
    return $this;
  }

  public function message($body) {
    $this->mail->isHTML(false);
    $this->mail->Body = $body;
    return $this;
  }

  public function template($template, $args = []) {
    $body = $this->view::renderEmail($template, $args);
    $this->mail->isHTML(true);
    $this->mail->Body = $body;
    return $this;
  }

  public function send() {
    try {
      $res = $this->mail->send();
    } catch (Exception $e) {
      return false;
    }

    $this->clear();
    return true;
  }
}

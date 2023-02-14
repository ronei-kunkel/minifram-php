<?php

namespace Minifram\Http;

use stdClass;

class Response {
  private $body = null;

  private $headers = [];

  private $statusCode = null;

  private $request = null;

  public function __construct(Request $request) {
    $this->request = $request;

    $this->clearHeaders();
  }

  private function clearHeaders() {
    // TODO: validate if really needed
    header_remove();
  }

  public function return($data = null, $httpCode = 200) {
    ($this->request->getFrom() === 'api')
      ? $this->json($data, $httpCode)
      : $this->html($data, $httpCode);

    $this->run();
  }

  private function json($data = null, $httpCode = 200) {
    if (is_null($data)) $data = new stdClass;

    $data = json_encode($data);

    $this->setApiHeaders()
      ->setStatus($httpCode)
      ->setBody($data);
  }

  private function setApiHeaders() {
    $this->setHeader('Content-Type', 'application/json');

    return $this;
  }

  private function html($data = null, $httpCode = 200) {
    // TODO: implement views
    // if(is_array($data) and isset($data['error'])) $data = View::error($data['error']);

    if(is_array($data) and isset($data['error'])) $data = '<h1>'.$data['error'].'</h1>';

    $this->setWebHeaders()
      ->setStatus($httpCode)
      ->setBody($data);
  }

  private function setWebHeaders() {
    $this->setHeader('Content-Type', 'text/html; charset=UTF-8');

    return $this;
  }

  private function setStatus($httpCode){
    $this->statusCode = $httpCode;

    return  $this;
  }

  private function setBody($data) {
    $this->body = $data;
  }

  private function setHeader($name, $value) {
    $this->headers[$name] = $value;

    return $this;
  }

  private function run() {

    if(DEBUG_REQUEST_RESPONSE) {
      echo "<pre>";
      print_r($this);
      echo "</pre>";
    }

    // TODO: implement logs

    $this->setHeaders()
      ->setOutput();

    exit;
  }

  private function setHeaders() {

    header('HTTP/1.1 '.$this->statusCode);

    foreach ($this->headers as $name => $value) {
      header($name . ': ' . $value, true);
    }

    return $this;
  }

  private function setOutput() {
    if (!is_null($this->body)) {
      $phpStdOut = fopen('php://output', 'w');
      fwrite($phpStdOut, $this->body);
      fclose($phpStdOut);
    }
  }
}

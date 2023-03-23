<?php

namespace Minifram\Http;

class Request {

  // TODO: validate xss attack
  // TODO: validade sqlInjection

  private $from = null;

  private $uri = [];

  private $method = null;

  private $resource = null;

  private $id = null;

  private $action = null;

  private $route = null;

  private $body = null;

  private $headers = [];

  public function __construct() {
    $this->from = $_REQUEST['routes'];

    $this->padronizeUri();

    $this->setMethod()
      ->setResource()
      ->setId()
      ->setAction()
      ->setRoute()
      ->setBody()
      ->setHeaders();
  }

  /**
   * Set Methods
   */

  private function setBody() {
    $wrapper = fopen('php://input', 'r');
    $this->body = preg_replace('/(\n)?([\s])+/', '', stream_get_contents($wrapper));
    fclose($wrapper);
    return $this;
  }

  private function setHeaders() {
    $this->headers = apache_request_headers();
    return $this;
  }

  private function setMethod() {
    $this->method = $_SERVER['REQUEST_METHOD'];
    return $this;
  }

  private function setResource() {
    $this->resource = ($this->uri['resource'] === '/') ? '/' : $this->uri['resource'];
    return $this;
  }

  private function setId() {
    $this->id = (empty($this->uri['id']) and $this->uri['id'] !== '0') ? null : $this->uri['id'];
    return $this;
  }

  private function setAction() {
    $this->action = empty($this->uri['action']) ? null : $this->uri['action'];
    return $this;
  }

  private function setRoute() {
    $this->route = ($this->uri['resource'] === '/') ? $this->uri['resource'] : '/' . $this->uri['resource'];

    if (!empty($this->uri['id'])) $this->route .= '/' . $this->uri['id'];

    if (!empty($this->uri['action'])) $this->route .= '/' . $this->uri['action'];
    return $this;
  }


  /**
   * Get Methods
   */

  public function getBody() {
    $body = ($this->getFrom() === 'api') ? json_decode($this->body, true) : $this->body;
    if(!$body) exit; (new Response($this))->return(['error' => 'Invalid body'], 400);
    return $body;
  }

  public function getHeaders() {
    return $this->headers;
  }

  public function getFrom() {
    return $this->from;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getResource() {
    return $this->resource;
  }

  public function getId() {
    return $this->id;
  }

  public function getAction() {
    return $this->action;
  }

  public function getRoute() {
    return $this->route;
  }

  public function getLimit() {
    return $this->uri['limit'] ?? null;
  }

  public function getOffset() {
    return $this->uri['offset'] ?? null;
  }

  /**
   * Miscellaneous Methods
   */

   // TODO: refactor method after done inside TODOs
  private function padronizeUri() { 
    // TODO: extract to new method called convertUri
    $requestUri = $_REQUEST;

    $uri = array_values(array_filter(explode('/', $requestUri['uri']), function($value) {
      return ($value !== null && $value !== false && $value !== '');
    }));

      // TODO: transform this block in validation with CONSTANT to check if enable multisite in same infrastructure
      // anything like $this->checkMultisite($uri);
      // and the method recieve the variable with reference (&$uri)
    unset($uri[0]);
    $uri = array_values($uri);
      // until here

    unset($requestUri['uri']);

    if(empty($uri)) $uri = [
      0 => '/',
      1 => '',
      2 => ''
    ];
    // until here

    // TODO: extract to new method called convertApiUri with reference value for $uri
    if($uri[0] === 'api') {
      unset($uri[0]);
      $cleanUri = array_values($uri);
      $uri = $cleanUri;
      unset($cleanUri);
    }
    // until here

    $uri['resource'] = $uri[0] ?? '/';
    $uri['id']       = $uri[1] ?? '';
    $uri['action']   = $uri[2] ?? '';
    unset($uri[0]);
    unset($uri[1]);
    unset($uri[2]);

    foreach ($uri as $key => $value) {
      $requestUri[$key] = $value;
    }

    $this->uri = $requestUri;
    return $this;
  }
}

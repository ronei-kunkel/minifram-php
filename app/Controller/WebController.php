<?php

namespace Minifram\Controller;

use Minifram\Http\Request;
use Minifram\Http\Response;

class WebController extends Controller {

  public static function index(Request $request) {
    self::$request = $request;

    (new Response($request))->return('<h1>Hello From Web Controller</h1>');
  }
}

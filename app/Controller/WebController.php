<?php

namespace Minifram\Controller;

use Minifram\Http\Request;

class WebController extends Controller {

  public static function index(Request $request) {
    self::$request = $request;

    $request->sendResponse('<h1>Hello From Web Controller</h1>');
  }
}

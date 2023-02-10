<?php

namespace Minifram\Controller;

use Minifram\Http\Request;

class ApiController extends Controller {

  public static function api(Request $request) {
    self::$request = $request;
    
    $request->sendResponse(['method' => __METHOD__]);
  }
}

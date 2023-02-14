<?php

namespace Minifram\Controller;

use Minifram\Http\Request;
use Minifram\Http\Response;

class ApiController extends Controller {

  public static function check(Request $request) {
    self::$request = $request;

    (new Response($request))->return();
  }
}

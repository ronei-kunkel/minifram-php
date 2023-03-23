<?php

namespace Minifram\Provider;

use Minifram\Http\Request;
use Minifram\Http\Response;

class RouterServiceProvider {

  /**
   * Path to routes folder
   *
   * @var string
   */
  private static $routesPath = __DIR__ . '/../../routes/';

  /**
   * Include the specific file of routes according to nginx rule
   * 
   * If endpoint init with "/api" load api.php file of routes
   * 
   * Otherwise load web.php file of routes if exists
   *
   * @param Request $request
   * @return void
   */
  public static function load(Request $request) {

    $routesFile = self::$routesPath . $request->getFrom() . '.php';

    if (!file_exists($routesFile)) (new Response($request))->return(['error' => ucfirst($request->getFrom()) .' routes aren\'t enabled.'], 404);

    include_once $routesFile;
  }
}

<?php

namespace Minifram\Controller;

use Minifram\Http\Request;
use Minifram\Http\Response;

class WebController extends Controller {

  public static function index(Request $request) {
    self::$request = $request;

    // TODO: implement views
    $html = '<!DOCTYPE html>
    <html lang="pt-br">
    <head>


      <!-- implementation of public files -->
      <link rel="stylesheet" href="public/css/styles.css">
      <link rel="icon" type="image/x-icon" href="public/favicon.ico">


      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
    </head>
    <body>
    <h1>Hello From Web Controller</h1>
    </body>
    </html>';

    (new Response($request))->return($html);
  }
}

<?php

use Minifram\Http\Request;
use Minifram\Provider\EnvServiceProvider;
use Minifram\Provider\RouterServiceProvider;
use Minifram\Router\Router;

EnvServiceProvider::load();

$request = new Request();

if (DEBUG or APP_ENV == 'dev') {
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
}

RouterServiceProvider::load($request);

Router::run($request);

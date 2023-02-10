<?php

use Minifram\Http\Request;
use Minifram\Provider\RouterServiceProvider;
use Minifram\Router\Router;

$request = new Request();

RouterServiceProvider::loadRoutes($request);

Router::run($request);

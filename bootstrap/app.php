<?php

use Minifram\Http\Request;
use Minifram\Provider\RouterServiceProvider;
use Minifram\Router\Router;

$request = new Request();

RouterServiceProvider::load($request);

Router::run($request);

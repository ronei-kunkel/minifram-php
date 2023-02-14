<?php

use Minifram\Router\Router;

// TODO: implement middlewares with sintax ->withAuth->withThrottling()->withCache
Router::get('/', 'Minifram\Controller\ApiController::check');
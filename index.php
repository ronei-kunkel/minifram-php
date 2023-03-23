<?php

use Minifram\Provider\EnvServiceProvider;

require_once __DIR__.'/vendor/autoload.php';

EnvServiceProvider::load();

if (DEBUG or APP_ENV == 'dev') {
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
}

include_once __DIR__.'/bootstrap/app.php';

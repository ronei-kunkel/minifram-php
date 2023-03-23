<?php

// set default timezone of system
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/Sao_Paulo');

// set default environment of application
define('APP_ENV', $_ENV['APP_ENV'] ?? 'prod');

// set false to increase security or true while develop
define('DEBUG', $_ENV['DEBUG'] ?? false);

// set true to show what system receive and what system returned
define('DEBUG_REQUEST_RESPONSE', $_ENV['DEBUG_REQUEST_RESPONSE'] ?? false);

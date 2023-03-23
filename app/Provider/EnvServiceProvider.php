<?php

namespace Minifram\Provider;

use Symfony\Component\Dotenv\Dotenv;

class EnvServiceProvider {

  public static function load() {
    $dotenv = new Dotenv();
    $dotenv->load('.env');
  }

}

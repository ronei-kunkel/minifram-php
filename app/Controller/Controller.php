<?php

namespace Minifram\Controller;

use Minifram\Http\Request;

abstract class Controller {

  /**
   * Request to use in class context
   *
   * @var Request
   */
  protected static Request $request;
}

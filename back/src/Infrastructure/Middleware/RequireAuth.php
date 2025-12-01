<?php

namespace App\Infrastructure\Middleware;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class RequireAuth
{
  public function __construct() {}
}

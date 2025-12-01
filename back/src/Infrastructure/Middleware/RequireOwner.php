<?php

namespace App\Infrastructure\Middleware;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class RequireOwner
{
  public function __construct() {}
}

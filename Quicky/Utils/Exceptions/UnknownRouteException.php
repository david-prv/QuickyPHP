<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Utils\Exceptions;

use Throwable;

/**
 * Class UnknownRouteException
 */
class UnknownRouteException extends NetworkException
{
    protected string $default = "No route found for '%s'. It may not exist.";

    public function __construct($routeURL, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $routeURL), $code, $previous);
    }
}

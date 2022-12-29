<?php

declare(strict_types=1);

class UnknownRouteException extends NetworkException
{
    protected string $default = "No route found for '%s'. It may not exist.";

    public function __construct($routeURL, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $routeURL), $code, $previous);
    }
}
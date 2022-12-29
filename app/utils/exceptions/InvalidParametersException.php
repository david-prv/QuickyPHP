<?php

declare(strict_types=1);

class InvalidParametersException extends CoreException
{
    protected string $default = "The passed parameter '%s' is either not existing or invalid.";

    public function __construct($paramName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $paramName), $code, $previous);
    }
}
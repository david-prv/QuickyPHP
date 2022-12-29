<?php

declare(strict_types=1);

class InvalidClassException extends CoreException
{
    protected string $default = "The method name '%s' could not be resolved. It may not exist.";

    public function __construct($className, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $className), $code, $previous);
    }
}
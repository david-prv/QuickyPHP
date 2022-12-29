<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

class UnknownCallException extends NetworkException
{
    protected string $default = "The method '%s' could not be dispatched. Did you made a typo?";

    public function __construct($methodName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $methodName), $code, $previous);
    }
}
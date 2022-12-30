<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class UnknownCallException
 */
class UnknownCallException extends NetworkException
{
    protected string $default = "The method '%s' could not be dispatched (may be a missing @dispatch" .
                                " annotation in PHPDocs)";

    public function __construct($methodName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $methodName), $code, $previous);
    }
}
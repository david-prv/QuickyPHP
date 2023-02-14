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
 * Class UnknownCallException
 */
class UnknownMethodException extends NetworkException
{
    protected string $default = "The method '%s' is unknown.";

    public function __construct($methodName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $methodName), $code, $previous);
    }
}

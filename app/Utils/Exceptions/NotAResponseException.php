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
class NotAResponseException extends NetworkException
{
    protected string $default = "The returned response was not an instance of Response. Maybe you " .
    "forgot to return the response in your route definitions?";

    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->default ?? $message, $code, $previous);
    }
}

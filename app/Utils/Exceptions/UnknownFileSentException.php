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
class UnknownFileSentException extends NetworkException
{
    protected string $default = "File '%s' could not be found or permission denied.";

    public function __construct($fileName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $fileName), $code, $previous);
    }
}

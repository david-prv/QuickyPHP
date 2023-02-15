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
 * Class InvalidClassException
 */
class InsufficientAliasException extends CoreException
{
    protected string $default = "The method alias for '%s' could not be created: Missing or too many args!";

    public function __construct($master, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $master), $code, $previous);
    }
}

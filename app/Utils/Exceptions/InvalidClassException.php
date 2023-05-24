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
class InvalidClassException extends CoreException
{
    protected string $default = "The method name '%s' could not be resolved. It may not exist.";

    public function __construct($className, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $className), $code, $previous);
    }
}

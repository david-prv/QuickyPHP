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
 * Class InvalidParametersException
 */
class InvalidParametersException extends CoreException
{
    protected string $default = "The passed parameter '%s' is either not existing or invalid.";

    public function __construct($paramName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $paramName), $code, $previous);
    }
}

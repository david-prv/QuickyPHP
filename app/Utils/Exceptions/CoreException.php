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

use Exception;
use Throwable;

/**
 * Class CoreException
 */
class CoreException extends Exception
{
    protected string $default = "Critical Error! An error has occurred in a core function. " .
    "Please report that to a maintainer or your system administrator.";

    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? $this->default, $code, $previous);
    }
}

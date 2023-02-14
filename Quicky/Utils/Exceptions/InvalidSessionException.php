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
 * Class InvalidSessionException
 */
class InvalidSessionException extends CoreException
{
    protected string $default = "The session could not be constructed.";

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct($this->default, $code, $previous);
    }
}

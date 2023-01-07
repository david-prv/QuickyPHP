<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Utils\Exceptions;

use Throwable;

/**
 * Class MySQLConnException
 */
class MySQLConnException extends Exception
{
    protected string $default = "Critical Error! Could not establish a connection or execute query!";

    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? $this->default, $code, $previous);
    }
}
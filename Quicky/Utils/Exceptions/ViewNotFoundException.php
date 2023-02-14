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
 * Class ViewNotFoundException
 */
class ViewNotFoundException extends CoreException
{
    protected string $default = "The view '%s' could not be found. It may not exist.";

    public function __construct($viewName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->default, $viewName), $code, $previous);
    }
}

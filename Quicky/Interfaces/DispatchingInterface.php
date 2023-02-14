<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Interfaces;

/**
 * Interface DispatchingInterface
 */
interface DispatchingInterface
{
    /**
     * Check if class is dispatching the
     * function, identified by name
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool;
}

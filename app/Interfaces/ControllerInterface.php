<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky\Interfaces;

/**
 * Interface ControllerInterface
 */
interface ControllerInterface
{
    public function setup(...$params): void;
}

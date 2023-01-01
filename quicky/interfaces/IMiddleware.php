<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Interface IMiddleware
 */
interface IMiddleware
{
    /**
     * Execute the middleware
     */
    public function run(): void;

    /**
     * Execute next callable
     */
    public function next(): void;
}
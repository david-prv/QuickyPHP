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
     * Execute middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function run(Request $request, Response $response, callable $next): ?Response;
}
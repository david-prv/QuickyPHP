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
 * Class ExampleMiddleware
 */
class ExampleMiddleware implements IMiddleware
{
    /**
     * Run middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response|null
     */
    public function run(Request $request, Response $response, callable $next): ?Response
    {
        $response->send("<h3>Middleware</h3>");
        $response = $next($request, $response);
        return $response;
    }
}
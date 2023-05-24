<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Middleware;

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Interfaces\MiddlewareInterface;

/**
 * Class CORSMiddleware
 */
class CORSMiddleware implements MiddlewareInterface
{
    /**
     * Run middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response|null
     */
    public function run(Request $request, Response $response, callable $next): Response
    {
        // Allow cross-origin requests from any domain
        $response->withHeader('Access-Control-Allow-Origin', '*');

        // Allow specified HTTP methods
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, HEAD');

        // Allow specified HTTP headers
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Allow cookies to be included in CORS requests
        $response->withHeader('Access-Control-Allow-Credentials', 'true');

        return $next($request, $response);
    }
}

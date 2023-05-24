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

use Quicky\Http\Request;
use Quicky\Http\Response;

/**
 * Interface MiddlewareInterface
 */
interface MiddlewareInterface
{
    /**
     * Execute middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function run(Request $request, Response $response, callable $next): Response;
}

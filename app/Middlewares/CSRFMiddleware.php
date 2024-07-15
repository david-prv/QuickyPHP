<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Middlewares;

use Quicky\Core\DynamicLoader;
use Quicky\Core\Repositories\SessionRepository;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Interfaces\MiddlewareInterface;

/**
 * Class CSRFMiddleware
 */
class CSRFMiddleware implements MiddlewareInterface
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
        $session = DynamicLoader::getLoader()->getInstance(SessionRepository::class);
        if ($session instanceof SessionRepository) {
            if (!$request->hasCSRFToken() || !$session->verifyCSRF($request->getCSRFToken())) {
                // send forbidden message and error code
                $response->status(403);

                // break middleware execution
                $response->stop(403);
            }
        }
        return $next($request, $response);
    }
}

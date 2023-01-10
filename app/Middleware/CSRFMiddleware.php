<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\DynamicLoader;
use App\Core\Managers\SessionManager;
use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;

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
        $session = DynamicLoader::getLoader()->getInstance(SessionManager::class);
        if ($session instanceof SessionManager) {
            if (!$request->hasCSRFToken() || !$session->verifyCSRF($request->getCSRFToken())) {
                // send forbidden message and error code
                $response->forbidden($response->getErrorMessage(403));

                // break middleware execution
                $response->stop();
            }
        }
        return $next($request, $response);
    }
}

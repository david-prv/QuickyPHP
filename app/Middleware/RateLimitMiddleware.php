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

use Quicky\Core\DynamicLoader;
use Quicky\Core\Managers\SessionManager;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Interfaces\MiddlewareInterface;

/**
 * Class RateLimitMiddleware
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    /**
     * Max possible requests
     *
     * @var int
     */
    protected int $maxRequests;

    /**
     * Time period in seconds
     * for max requests
     *
     * @var int
     */
    protected int $timePeriod;

    /**
     * RateLimitMiddleware constructor.
     *
     * @param int $maxRequests
     * @param int $timePeriod
     */
    public function __construct(int $maxRequests, int $timePeriod)
    {
        $this->maxRequests = $maxRequests;
        $this->timePeriod = $timePeriod;
    }

    /**
     * Run middleware
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response|null
     */
    public function run(Request $request, Response $response, callable $next): Response
    {
        // skip if session is inactive
        $session = DynamicLoader::getLoader()->getInstance(SessionManager::class);
        if ($session instanceof SessionManager) {
            if (!$session->isActive()) {
                return $next($request, $response);
            }
        }

        $ipAddress = $request->getRemote()[0];
        $key = "rate_limit_{$ipAddress}";

        if (!isset($_SESSION[$key])) {
            // Initialize rate limiting for this IP address
            $_SESSION[$key] = [
                'count' => 1,
                'timestamp' => time()
            ];
        } else {
            $rateLimit = $_SESSION[$key];
            $elapsed = time() - $rateLimit['timestamp'];

            if ($elapsed > $this->timePeriod) {
                // Reset the rate limit if the time period has expired
                $rateLimit = [
                    'count' => 1,
                    'timestamp' => time()
                ];
            } elseif ($rateLimit['count'] < $this->maxRequests) {
                // Increment the count
                $rateLimit['count']++;
            } else {
                // Return a response with an error message
                $response->stop(429);
            }

            // Update the rate limit in the session
            $_SESSION[$key] = $rateLimit;
        }

        // Pass the request on to the next middleware
        return $next($request, $response);
    }
}

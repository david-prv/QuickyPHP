<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Http;

use Quicky\Core\Config;
use Quicky\Core\DynamicLoader;
use Quicky\Interfaces\DispatchingInterface;
use Quicky\App;
use Quicky\Utils\Exceptions\NetworkException;
use Quicky\Utils\Exceptions\NotAResponseException;
use Quicky\Utils\Exceptions\UnknownMethodException;
use Quicky\Utils\Exceptions\UnknownRouteException;

/**
 * Class Router
 */
class Router implements DispatchingInterface
{
    /**
     * All existing routes
     * (Associative array)
     *
     * @var array
     */
    private array $routes;

    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Universal middleware
     *
     * @var array
     */
    private array $middleware;

    /**
     * Router's cache
     *
     * @var array
     */
    private array $cache;

    /**
     * Array of all allowed methods
     *
     * @var array
     */
    private array $methods;

    /**
     * Local cache file
     *
     * @var string
     */
    private string $cacheFile = 'router.cache';

    /**
     * Router constructor.
     *
     * @param string $cachePath
     */
    public function __construct(string $cachePath = "/cache")
    {
        $this->routes = array();
        $this->dispatching = array("router", "route", "group");
        $this->middleware = array();
        $this->methods = array("GET", "POST", "PUT", "PATCH", "UPDATE", "DELETE");
        $this->cacheFile = getcwd() . "$cachePath/" . $this->cacheFile;
        $this->cache = $this->loadCache();
    }

    /**
     * Loads cache from cache file
     *
     * @return array
     */
    private function loadCache(): array
    {
        if (!DynamicLoader::getLoader()->getInstance(Config::class)->isCacheActive()) {
            return array();
        }
        if (!file_exists($this->cacheFile)) {
            return [];
        }
        $cache = file_get_contents($this->cacheFile);
        return unserialize($cache);
    }

    /**
     * Saves cache to cache file
     */
    private function saveCache(): void
    {
        if (!DynamicLoader::getLoader()->getInstance(Config::class)->isCacheActive()) {
            return;
        }
        $serializedCache = serialize($this->cache);
        file_put_contents($this->cacheFile, $serializedCache);
    }

    /**
     * Invoked method
     *
     * Finds a route depending on the request
     * and executes it with a proper response object
     *
     * @param Request $request
     * @param Response $response
     * @throws UnknownRouteException
     * @throws NotAResponseException
     */
    public function __invoke(Request $request, Response $response)
    {
        if (count($this->routes) === 0) {
            throw new UnknownRouteException($request->getUrl());
        }
        $route = $this->findRoute($request);
        if (is_null($route)) {
            throw new UnknownRouteException($request->getUrl());
        }
        if (App::session()->isSecure()) {
            App::session()->regenerateId();
        }

        $response = $route->execute($request, $response);

        if ($response instanceof Response) {
            $response->send();
        } else {
            throw new NotAResponseException();
        }
    }

    /**
     * Return router instance
     *
     * @return Router
     * @throws NetworkException
     */
    public static function router(): Router
    {
        $instance = DynamicLoader::getLoader()->getInstance(Router::class);

        if ($instance instanceof Router) {
            return $instance;
        } else {
            throw new NetworkException();
        }
    }

    /**
     * Returns route count
     *
     * @return int
     */
    public function countRoutes(): int
    {
        return count($this->routes);
    }

    /**
     * Set universal middleware
     *
     * @param mixed ...$middleware
     */
    public function useMiddleware(...$middleware): void
    {
        $this->middleware = $middleware;
    }

    /**
     * Checks whether a method is valid
     *
     * @param string $method
     * @return bool
     */
    private function isValidMethod(string $method): bool
    {
        $method = strtoupper($method);
        return in_array($method, $this->methods);
    }

    /**
     * Add GET route
     *
     * @param string $method
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function route(string $method, string $pattern, callable $callback, ...$middleware): void
    {
        if (!$this->isValidMethod($method)) {
            new UnknownMethodException($method);
        }

        $middleware = array_merge($middleware, $this->middleware);
        $route = new Route(strtoupper($method), $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Group a bunch of route definitions together
     * and enable them by predicate
     *
     * @param $predicate
     * @param callable $definitions
     * @return void
     */
    public function group($predicate, callable $definitions): void
    {
        if (!call_user_func($predicate)) {
            return;
        }

        call_user_func($definitions);
    }

    /**
     * Dump stored nodes as HTML
     */
    public function dump(): void
    {
        echo "<strong>Router Dump: (total: " . $this->countRoutes() . ")</strong><br>";
        foreach ($this->routes as $route) {
            echo $route->toString() . "<br>";
        }
    }

    /**
     * Checks if a route is contained in
     * the routes array
     *
     * @param Route $route
     * @return bool
     */
    private function isRoute(Route $route): bool
    {
        return (isset($this->routes[$route->hashCode()]));
    }

    /**
     * Asks router cache if the
     * method-url-combination was already seen before
     *
     * @param Request $request
     * @param $method
     * @param string $url
     * @return Route|null
     */
    private function resolveRouteCache(Request $request, $method, string $url): ?Route
    {
        if (isset($this->cache["$method.$url"])) {
            // check whether the cached route is still existing
            if (isset($this->routes[$this->cache["$method.$url"]])) {
                $route = $this->routes[$this->cache["$method.$url"]];
                if ($route instanceof Route) {
                    // check whether the cached route matches the url
                    if ($route->match($url, $request)) {
                        return $route;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Finds route by hash-code
     *
     * @param string $hash
     * @return Route|null
     */
    private function findRouteByHash(string $hash): ?Route
    {
        return (isset($this->routes[$hash])) ? $this->routes[$hash] : null;
    }

    /**
     * Finds route that fits to requested url
     *
     * @param Request $request
     * @return Route|null
     */
    private function findRoute(Request $request): ?Route
    {
        $url = $request->getUrl();
        $method = $request->getMethod();

        // trivial route can be matched by hash
        if ($url === "/") {
            // it can happen that trivial routes are created by
            // non-trivial patterns, like super-wildcards
            $route = $this->findRouteByHash(sha1($url . $method));
            if (!is_null($route)) {
                // if it was really a trivial route,
                // return the found route
                return $route;
            }
        }

        // ask cache first
        $cacheResult = $this->resolveRouteCache($request, $method, $url);
        if (!is_null($cacheResult)) {
            return $cacheResult;
        }

        // find matching route
        foreach ($this->routes as $route) {
            if ($route instanceof Route) {
                if ($route->match($url, $request)) {
                    $this->cache["$method.$url"] = $route->hashCode();
                    $this->saveCache();
                    return $route;
                }
            }
        }
        return null;
    }

    /**
     * Checks if class is dispatching
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool
    {
        return in_array($method, $this->dispatching);
    }
}

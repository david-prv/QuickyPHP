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
 * Class Router
 */
class Router implements IDispatching
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
     * Router constructor.
     */
    public function __construct()
    {
        $this->routes = array();
        $this->dispatching = array("router", "get", "post", "put", "update", "delete", "patch");
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
     */
    public function __invoke(Request $request, Response $response)
    {
        if (count($this->routes) === 0) throw new UnknownRouteException($request->getUrl());
        $route = $this->findRoute($request);
        if (is_null($route)) throw new UnknownRouteException($request->getUrl());

        if (Quicky::session()->isSecure()) Quicky::session()->regenerateId();

        $route->execute($request, $response);
    }

    /**
     * Return router instance
     *
     * @return object|Router|null
     * @throws NetworkException
     */
    public function router()
    {
        $instance = DynamicLoader::getLoader()->getInstance(Router::class);

        if ($instance instanceof Router) return $instance;
        else throw new NetworkException();
    }

    /**
     * Add GET route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function get(string $pattern, callable $callback, ...$middleware): void
    {
        $route = new Route("GET", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Add POST route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function post(string $pattern, callable $callback,  ...$middleware): void
    {
        $route = new Route("POST", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Add PUT route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function put(string $pattern, callable $callback, ...$middleware): void
    {
        $route = new Route("PUT", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Add UPDATE route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function update(string $pattern, callable $callback, ...$middleware): void
    {
        $route = new Route("UPDATE", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Add DELETE route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function delete(string $pattern, callable $callback, ...$middleware): void
    {
        $route = new Route("DELETE", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * Add PATCH route
     *
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function patch(string $pattern, callable $callback, ...$middleware): void
    {
        $route = new Route("PATCH", $pattern, $callback, $middleware);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
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
        foreach ($this->routes as $r) {
            if ($route->hashCode() === $r->hashCode()) return true;
        }
        return false;
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

        // Trivial route
        if ($url === "/") {
            return $this->findRouteByHash(sha1($url . $method));
        }

        foreach ($this->routes as $route) {
            if ($route instanceof Route) {
                if ($route->match($url, $request)) return $route;
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
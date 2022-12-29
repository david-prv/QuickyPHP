<?php

declare(strict_types=1);

class Router
{
    private array $routes;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->routes = array();
    }

    /**
     * @param Request $request
     * @throws UnknownRouteException
     */
    public function __invoke(Request $request)
    {
        $route = $this->findRoute(sha1($request->getUrl().$request->getMethod()));
        if (is_null($route)) throw new UnknownRouteException($request->getUrl());

        $route->execute();
    }

    /**
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
     * @param string $hash
     * @return Route|null
     */
    private function findRoute(string $hash): ?Route
    {
        return (isset($this->routes[$hash])) ? $this->routes[$hash] : null;
    }

    /**
     * @param string $pattern
     * @param callable $callback
     */
    public function get(string $pattern, callable $callback): void
    {
        $route = new Route("GET", $pattern, $callback);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    /**
     * @param string $pattern
     * @param callable $callback
     */
    public function post(string $pattern, callable $callback): void
    {
        $route = new Route("POST", $pattern, $callback);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }
}
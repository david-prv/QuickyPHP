<?php

declare(strict_types=1);

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    private function isRoute(Route $route): bool
    {
        foreach ($this->routes as $r) {
            if ($route->hashCode() === $r->hashCode()) return true;
        }
        return false;
    }

    private function findRoute(string $hash): ?Route
    {
        foreach ($this->routes as $r) {
            if ($hash === $r->hashCode()) return $r;
        }
        return null;
    }

    public function get(string $pattern, callable $callback): void
    {
        $route = new Route("GET", $pattern, $callback);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

    public function post(string $pattern, callable $callback): void
    {
        $route = new Route("POST", $pattern, $callback);

        if (!$this->isRoute($route)) {
            $this->routes[$route->hashCode()] = $route;
        }
    }

}
<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Http;

/**
 * Class Route
 */
class Route
{
    /**
     * The method for the route
     *
     * @var string
     */
    private string $method;

    /**
     * A matching pattern for the route
     *
     * @var string
     */
    private string $pattern;

    /**
     * Provided middleware
     *
     * @var array
     */
    private array $middleware;

    /**
     * The callback method
     *
     * @var callable
     */
    private $callback;

    /**
     * Route constructor.
     *
     * @param string $method
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     */
    public function __construct(
        string $method,
        string $pattern,
        callable $callback,
        array $middleware
    ) {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callback = $callback;
        $this->middleware = $middleware;
    }

    /**
     * Returns the route hashcode
     *
     * @return string
     * @uses sha1()
     */
    public function hashCode(): string
    {
        return sha1($this->pattern . $this->method);
    }

    /**
     * Checks if middleware is used
     *
     * @return bool
     */
    private function usesMiddleware(): bool
    {
        return (count($this->middleware) >= 1);
    }

    /**
     * Executes the route closure
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function execute(Request $request, Response $response)
    {
        $middleware = $this->middleware;
        $callback = $this->callback;

        // Define the initial "next" function
        $next = function (Request $request, Response $response) use ($callback) {
            return call_user_func($callback, $request, $response);
        };

        // Loop through the middleware in reverse order
        for ($i = count($middleware) - 1; $i >= 0; $i--) {
            // Define the next middleware function
            $next = function (Request $request, Response $response) use ($middleware, $i, $next) {
                $currentMiddleware = $middleware[$i];
                return $currentMiddleware->run($request, $response, $next);
            };
        }

        // Execute the middleware chain
        return $next($request, $response);
    }

    /**
     * Returns the route in string format
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->method . " - " . $this->pattern . " (uses middleware: " .
            (($this->usesMiddleware()) ? "true" : "false") . ")";
    }

    /**
     * Returns a sanitized array of the
     * requested pattern
     *
     * @param string $pattern
     * @return array
     */
    private function getSanitizedPatternArray(string $pattern): array
    {
        return array_filter(explode("/", $pattern), function ($e) {
            return $e !== "" && $e !== null;
        });
    }

    /**
     * Finds the correct Regex pattern
     *
     * @param array $pattern
     * @return string
     */
    private function getUrlMatchingRegex(array $pattern): string
    {
        $regex = '';
        foreach ($pattern as $part) {
            if (preg_match("/^{.*}$/", $part)) {
                $regex .= '\/([^\/]+)';
            } elseif (preg_match("/^\(.*\)$/", $part)) {
                $part = str_replace(["(", ")"], "", $part);
                $regex .= "\/$part";
            } elseif ($part === '*') {
                $regex .= '\/.*';
            } elseif ($part === '**') {
                $regex .= '\/(.*)';
            } else {
                $regex .= "\/$part";
            }
        }
        return '/^' . $regex . '$/';
    }

    /**
     * Parses all named variables from URL
     *
     * @param array $pattern
     * @param array $urlParts
     * @return array
     */
    private function getUrlVariableValues(array $pattern, array $urlParts): array
    {
        $values = array();
        foreach ($pattern as $i => $part) {
            if (preg_match("/^{.*}$/", $part)) {
                $varName = str_replace(["{", "}"], "", $part);
                $values[$varName] = $urlParts[$i];
            }
        }
        return $values;
    }

    /**
     * Checks if the route contains a special
     * double-star wildcard, called super-wildcards
     *
     * @return bool
     */
    private function containsSuperWildcard(): bool
    {
        return (strpos($this->pattern, "**") !== false);
    }

    /**
     * Checks if the requested url
     * matches this route and additionally parses
     * all arguments and updates the request, iff vars are present
     *
     * @param string $url
     * @param Request $request
     * @return bool
     */
    public function match(string $url, Request $request): bool
    {
        $pattern = $this->getSanitizedPatternArray($this->pattern);
        $urlParts = $this->getSanitizedPatternArray($url);

        if ((!$this->containsSuperWildcard()) && (count($pattern) !== count($urlParts))) {
            return false;
        }

        $regex = $this->getUrlMatchingRegex($pattern);
        if (!preg_match($regex, $url)) {
            return false;
        }

        $values = $this->getUrlVariableValues($pattern, $urlParts);
        $request->setArgs($values);
        return true;
    }
}

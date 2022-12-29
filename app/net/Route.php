<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

class Route
{
    private string $method;
    private string $pattern;
    private $callback;

    /**
     * Route constructor.
     * @param string $method
     * @param string $pattern
     * @param callable $callback
     */
    public function __construct(string $method, string $pattern, callable $callback)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function hashCode(): string
    {
        return sha1($this->pattern.$this->method);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function execute(Request $request, Response $response)
    {
        ($this->callback)($request, $response);
    }
}
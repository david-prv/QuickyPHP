<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

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
     */
    public function __construct(string $method, string $pattern, callable $callback)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * Returns the route hashcode
     *
     * @uses sha1()
     * @return string
     */
    public function hashCode(): string
    {
        return sha1($this->pattern.$this->method);
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
        ($this->callback)($request, $response);
    }
}
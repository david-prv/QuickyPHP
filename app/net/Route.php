<?php

declare(strict_types=1);

namespace app\net;

class Route
{
    private string $method;
    private string $pattern;
    private $callback;

    public function __construct(string $method, string $pattern, callable $callback)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callback = $callback;
    }
}
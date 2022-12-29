<?php

declare(strict_types=1);

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

    public function hashCode(): string
    {
        return sha1($this->pattern.$this->method);
    }

    #[\ReturnTypeWillChange]
    public function execute()
    {
        return call_user_func($this->callback);
    }
}
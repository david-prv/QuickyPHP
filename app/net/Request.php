<?php

declare(strict_types=1);

class Request
{
    private string $method;
    private string $url;

    /**
     * Request constructor.
     * @param string $method
     * @param string $url
     */
    public function __construct(string $method, string $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    /**
     * @return Request
     */
    public static function current(): Request
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        return new Request($method, $url);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
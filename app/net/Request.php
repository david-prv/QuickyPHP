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
 * Class Request
 */
class Request
{
    /**
     * The HTTP method
     *
     * @var mixed|string
     */
    private string $method;

    /**
     * The requested URL
     *
     * @var mixed|string
     */
    private string $url;

    /**
     * The time of request
     *
     * @var mixed|string
     */
    private string $time;

    /**
     * Sent cookies
     *
     * @var mixed|string
     */
    private string $cookie;

    /**
     * The Accept-Header
     *
     * @var mixed|string
     */
    private string $accept;

    /**
     * The HTTP referrer
     *
     * @var mixed|string
     */
    private string $referrer;

    /**
     * The user-agent
     *
     * @var mixed|string
     */
    private string $ua;

    /**
     * HTTPS used
     *
     * @var bool|mixed
     */
    private bool $secure;

    /**
     * All headers, unsorted
     *
     * @var array|false|mixed
     */
    private array $headers;

    /**
     * All remote information
     *
     * @var array|mixed
     */
    private array $remote;

    /**
     * The sent data
     *
     * Priority:
     * $_POST > $_GET
     *
     * @var array|mixed
     */
    private array $data;

    /**
     * Request constructor.
     * @param array $data
     */
    public function __construct(...$data)
    {
        if (is_null($data) || count($data) === 0) {
            $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
            $this->url = (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $this->time = (string)$_SERVER["REQUEST_TIME"];
            $this->headers = getallheaders();
            $this->remote = array($_SERVER["REMOTE_ADDR"], gethostbyaddr($_SERVER["REMOTE_ADDR"]), $_SERVER["REMOTE_PORT"]);
            $this->cookie = $this->headers["Cookie"];
            $this->accept = $this->headers["Accept"];
            $this->ua = $this->headers["User-Agent"];
            $this->secure = isset($_SERVER["HTTPS"]) && !is_null($_SERVER["HTTPS"]);
            $this->referrer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
            $this->data = (count($_POST) >= 1) ? $_POST : ((count($_GET) >= 1) ? $_GET : array());
        } else {
            $this->method = $data["method"];
            $this->url = $data["url"];
            $this->time = $data["time"];
            $this->headers = $data["headers"];
            $this->remote = $data["remote"];
            $this->cookie = $data["cookie"];
            $this->accept = $data["accept"];
            $this->ua = $data["ua"];
            $this->secure = $data["secure"];
            $this->referrer = $data["referrer"];
            $this->data = $data["data"];
        }
    }

    /**
     * Returns the method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the time
     *
     * @return mixed|string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Returns the cookie
     *
     * @return mixed|string
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Returns the accept-header
     *
     * @return mixed|string
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Returns the referrer
     *
     * @return mixed|string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * Returns the user-agent
     *
     * @return mixed|string
     */
    public function getUserAgent()
    {
        return $this->ua;
    }

    /**
     * Indicates whether HTTPS is used
     *
     * @return bool|mixed
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Returns all headers
     *
     * @return array|false|mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns all remote details
     *
     * @return array|mixed
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * Returns the requested url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Returns the sent data
     *
     * @return array|mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
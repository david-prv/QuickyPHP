<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Http;

use Quicky\App;
use Quicky\Utils\Exceptions\InvalidParametersException;

/**
 * Class Request
 */
class Request
{
    /**
     * Request Ref-ID
     *
     * @var string
     */
    private string $id;

    /**
     * The HTTP method
     *
     * @var string
     */
    private string $method;

    /**
     * The requested URL
     *
     * @var string
     */
    private string $url;

    /**
     * The time of request
     *
     * @var string
     */
    private string $time;

    /**
     * Sent cookies
     *
     * @var string
     */
    private string $cookie;

    /**
     * The Accept-Header
     *
     * @var string
     */
    private string $accept;

    /**
     * The HTTP referrer
     *
     * @var string
     */
    private string $referrer;

    /**
     * The user-agent
     *
     * @var string
     */
    private string $ua;

    /**
     * The CSRF token
     *
     * @var string|null
     */
    private ?string $csrfToken;

    /**
     * HTTPS used
     *
     * @var bool
     */
    private bool $secure;

    /**
     * All headers, unsorted
     *
     * @var array
     */
    private array $headers;

    /**
     * All remote information
     *
     * @var array
     */
    private array $remote;

    /**
     * The sent data
     *
     * Priority:
     * $_POST > $_GET
     *
     * @var array
     */
    private array $data;

    /**
     * Contains all arguments passed
     * dynamically in route
     *
     * @var array
     */
    private array $args;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->id = uniqid();
        $this->collectServerData();
    }

    /**
     * Collect all info from $_SERVER
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function collectServerData(): void
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->url = (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->time = (string)$_SERVER["REQUEST_TIME"];
        $this->headers = (function_exists("getallheaders")) ? getallheaders() : $this->readRequestHeaders();
        $this->remote = array($_SERVER["REMOTE_ADDR"],
            gethostbyaddr($_SERVER["REMOTE_ADDR"]), $_SERVER["REMOTE_PORT"]);
        $this->cookie = $this->headers["Cookie"] ?? "";
        $this->accept = $this->headers["Accept"] ?? "";
        $this->ua = $this->headers["User-Agent"] ?? "";
        $this->secure = isset($_SERVER["HTTPS"]);
        $this->referrer = $_SERVER["HTTP_REFERER"] ?? "";
        $this->data = (count($_POST) >= 1) ? $_POST : ((count($_GET) >= 1) ? $_GET : array());
        $this->args = array();
        $this->csrfToken = (isset($this->data[App::__SESSION_CSRF]))
            ? $this->data[App::__SESSION_CSRF]
            : ((isset($this->headers["X-CSRF-TOKEN"])) ? $this->headers["X-CSRF-TOKEN"] : null);
    }

    /**
     * Workaround for undefined
     * in-built function "getallheaders()"
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return array
     */
    private function readRequestHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * Returns the refID
     *
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
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
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * Returns the cookie
     *
     * @return string
     */
    public function getCookie(): string
    {
        return $this->cookie;
    }

    /**
     * Returns the accept-header
     *
     * @return string
     */
    public function getAccept(): string
    {
        return $this->accept;
    }

    /**
     * Returns the referrer
     *
     * @return string
     */
    public function getReferrer(): string
    {
        return $this->referrer;
    }

    /**
     * Returns the user-agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->ua;
    }

    /**
     * Indicates whether HTTPS is used
     *
     * @return mixed
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Returns all headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns whether a header is present
     *
     * @param string $headerName
     * @return bool
     */
    public function hasHeader(string $headerName): bool
    {
        return (isset($this->headers[$headerName]));
    }

    /**
     * Returns all remote details
     *
     * @return array
     */
    public function getRemote(): array
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
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns a single data field
     * of sent data
     *
     * @param string $name
     * @param bool $htmlEscape
     * @return string|null
     */
    public function getField(string $name, bool $htmlEscape = true): ?string
    {
        return (isset($this->getData()[$name]))
            ? (($htmlEscape) ? htmlspecialchars($this->getData()[$name]) : $this->getData()[$name])
            : null;
    }

    /**
     * Returns whether a token is provided
     *
     * @return bool
     */
    public function hasCSRFToken(): bool
    {
        return !is_null($this->csrfToken);
    }

    /**
     * Returns the CSRF token
     *
     * @return string|null
     */
    public function getCSRFToken(): ?string
    {
        return $this->csrfToken;
    }

    /**
     * Returns the passed args
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Updates the passed args
     *
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * Returns a specific argument
     * as url-decoded string
     *
     * @param string $argName
     * @param bool $decode
     * @param bool $htmlEscape
     * @return string
     * @throws InvalidParametersException
     */
    public function getArg(string $argName, bool $decode = true, bool $htmlEscape = true): string
    {
        if (isset($this->args[$argName])) {
            return ($decode)
                ? (($htmlEscape) ? htmlspecialchars(urldecode($this->args[$argName]))
                    : urldecode($this->args[$argName]))
                : (($htmlEscape) ? htmlspecialchars($this->args[$argName]) : $this->args[$argName]);
        } else {
            throw new InvalidParametersException($argName);
        }
    }

    /**
     * Returns the request in string format
     *
     * @return string
     */
    public function toString(): string
    {
        $out = "";
        foreach ($this as $name => $value) {
            if (!is_string($value) && !is_bool($value)) {
                $value = "[" . gettype($value) . "]";
            }
            if (is_bool($value)) {
                $value = ($value) ? "true" : "false";
            }
            $out .= "$name => $value <br>";
        }
        return $out;
    }
}

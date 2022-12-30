<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class Response
 *
 * @dispatch render
 */
class Response
{
    private string $storagePath;

    /**
     * All HTTP codes
     *
     * @var array|string[]
     */
    private array $codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->storagePath = getcwd() . "/quicky/storage";
    }

    /**
     * Updates the HTTP response code
     *
     * @param int $code
     */
    public function status(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Initiates a HTTP redirection
     *
     * @param string $destination
     */
    public function redirect(string $destination): void
    {
        http_redirect($destination);
    }

    /**
     * Sends text/html with formatters
     *
     * @param string $text
     * @param mixed ...$formatters
     */
    public function send(string $text, ...$formatters): void
    {
        printf($text, ...$formatters);
    }

    /**
     * Resolves the error message for a
     * HTTP error code (int)
     *
     * @param int $code
     * @return string
     */
    public function getErrorMessage(int $code): string
    {
        return (isset($this->codes[$code])) ? $this->codes[$code] : "Strange HTTP Error";
    }

    /**
     * Sends file-content as response
     *
     * @param string $fileName
     * @throws UnknownFileSentException
     */
    public function sendFile(string $fileName): void
    {
        $basePath = $this->storagePath;
        $fullPath = "$basePath/$fileName";

        if (strpos($fullPath, $basePath) !== 0) throw new UnknownFileSentException($fileName);
        if (!file_exists($fullPath)) throw new UnknownFileSentException($fileName);

        $contents = file_get_contents($fullPath);

        echo $contents;
    }

    /**
     * Renders a view as response
     *
     * @param string $viewName
     * @param array|null $variables
     * @param string|null $override
     * @throws ViewNotFoundException
     */
    public function render(string $viewName, ?array $variables = null, ?string $override = null): void
    {
        RenderEngine::display($viewName, $variables, $override);
    }

    /**
     * Returns response in string format
     *
     * @return string
     */
    public function toString(): string
    {
        // TODO: Implement something different here
        return gettype($this);
    }
}
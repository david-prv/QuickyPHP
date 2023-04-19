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

use Quicky\Core\Config;
use Quicky\Core\DynamicLoader;
use Quicky\Core\View;
use Quicky\Utils\Exceptions\UnknownFileSentException;
use Quicky\Utils\Exceptions\ViewNotFoundException;

/**
 * Class Response
 */
class Response
{
    /**
     * Storage path
     *
     * @var string
     */
    private string $storagePath;

    /**
     * Is cache active?
     *
     * @var bool
     */
    private bool $useCache;

    /**
     * Expiration of cache
     * iff it is enabled
     *
     * @var int|null
     */
    private ?int $cacheExpires;

    /**
     * Response body
     *
     * @var string
     */
    private string $body;

    /**
     * Flag that indicates,
     * whether the send() method was called
     *
     * @var bool
     */
    private bool $isSent;

    /**
     * Response headers
     *
     * @var array
     */
    private array $headers;

    /**
     * All MIME Types
     *
     * @var array|string[]
     */
    private array $mimeTypes = [
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asc' => 'text/plain',
        'atom' => 'application/atom+xml',
        'au' => 'audio/basic',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'cdf' => 'application/x-netcdf',
        'cgm' => 'image/cgm',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'html' => 'text/html',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'zip' => 'application/zip',
        'gzip' => 'application/gzip',
        'tar' => 'application/x-tar',
        'latex' => 'application/x-latex',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/x-wav',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'avi' => 'video/x-msvideo',
        'ico' => 'image/x-icon',
        'csv' => 'text/csv',
        'txt' => 'text/plain',
        'xml' => 'text/xml',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mif' => 'application/vnd.mif',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'ms' => 'application/x-troff-ms',
        'msh' => 'model/mesh',
        'mxu' => 'video/vnd.mpegurl',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'pbm' => 'image/x-portable-bitmap',
        'pdb' => 'chemical/x-pdb',
        'pgm' => 'image/x-portable-graymap',
        'pgn' => 'application/x-chess-pgn',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'ppt' => 'application/vnd.ms-powerpoint',
        'ps' => 'application/postscript',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'image/x-cmu-raster',
        'rdf' => 'application/rdf+xml',
        'rgb' => 'image/x-rgb',
        'rm' => 'application/vnd.rn-realmedia',
        'roff' => 'application/x-troff',
        'rss' => 'application/rss+xml',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'silo' => 'model/mesh',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'snd' => 'audio/basic',
        'so' => 'application/octet-stream',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'tsv' => 'text/tab-separated-values',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vrml' => 'model/vrml',
        'vxml' => 'application/voicexml+xml',
        'wbmp' => 'image/vnd.wap.wbmp',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'wrl' => 'model/vrml',
        'xbm' => 'image/x-xbitmap',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xpm' => 'image/x-xpixmap',
        'xsl' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xwd' => 'image/x-xwindowdump',
        'xyz' => 'chemical/x-xyz',
    ];

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
        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        $this->storagePath = getcwd() . $config->getStoragePath();
        $this->useCache = $config->isCacheActive();
        $this->cacheExpires = ($this->useCache) ? $config->getCacheExpiration() : null;
        $this->isSent = false;
        $this->headers = array();
        $this->body = "";
    }

    /**
     * Checks whether the response was
     * already sent as HTTP response or not
     *
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->isSent;
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
     * Stops/Halts HTTP Response
     * e.g if an error occurred
     *
     * @param int $code
     */
    public function stop(int $code = 200): void
    {
        die($this->getErrorMessage($code));
    }

    /**
     * Initiates a HTTP redirection
     *
     * @param string $destination
     */
    public function redirect(string $destination): void
    {
        $this->withHeader("Location", $destination);
    }

    /**
     * Set headers to use cache
     */
    private function withCacheHeaders(): void
    {
        if (is_null($this->cacheExpires) || !$this->useCache) {
            return;
        }

        $expire = time() + $this->cacheExpires;

        $this->withHeader("Cache-Control", "max-age=$expire");
        $this->withHeader("Expires", gmdate("D, d M Y H:i:s", $expire) . " GMT");
        $this->withHeader("Last-Modified", gmdate("D, d M Y H:i:s", time()) . " GMT");
    }

    /**
     * Sets all security-relevant headers
     *
     * @return void
     */
    private function withSecurityHeaders(): void
    {
        $this->withHeader("X-Frame-Options", "DENY");
        $this->withHeader("X-XSS-Protection", "1; mode=block");
        $this->withHeader("X-Content-Type-Options", "nosniff");
    }

    /**
     * Set a custom header
     *
     * @param string $headerName
     * @param string $headerValue
     */
    public function withHeader(string $headerName, string $headerValue): void
    {
        $this->headers[] = array("name" => $headerName, "value" => $headerValue);
    }

    /**
     * Apply all headers
     */
    private function applyHeaders(): void
    {
        if (count($this->headers) === 0 || $this->isSent()) {
            return;
        }

        foreach ($this->headers as $header) {
            header($header["name"] . ": " . $header["value"]);
        }
    }

    /**
     * Get current body size
     *
     * @return int
     */
    public function getContentLength(): int
    {
        return strlen($this->body);
    }

    /**
     * Get current body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Writes text/html with formatters
     * to response body
     *
     * @param string $text
     * @param mixed ...$formatters
     */
    public function write(string $text, ...$formatters): void
    {
        if ($this->isSent()) {
            return;
        }
        $this->body .= sprintf($text, ...$formatters) . PHP_EOL;
    }

    /**
     * Compress the response with gzencode.
     * Should only be called as last statement in callback,
     * before response is returned.
     *
     * @param int $level
     */
    private function compress(int $level = 6): void
    {
        $this->body = gzencode($this->body, $level);

        $this->withHeader("Content-Type", "application/x-download");
        $this->withHeader("Content-Encoding", "gzip");
        $this->withHeader("Content-Length", (string)$this->getContentLength());
        $this->withHeader("Content-Disposition", "attachment; filename='compressed.data.gz'");
        $this->withHeader("Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
        $this->withHeader("Pragma", "no-cache");
    }

    /**
     * Resolves the error message for a
     * HTTP error code (int)
     *
     * @param int $code
     * @return string
     */
    private function getErrorMessage(int $code): string
    {
        return (isset($this->codes[$code])) ? $this->codes[$code] : "Strange HTTP Error";
    }

    /**
     * Resolve MIME Type
     *
     * @param string $fileName
     * @return string
     */
    private function getMIMEType(string $fileName): string
    {
        return (isset($this->mimeTypes[strtolower(substr($fileName, strrpos($fileName, '.') + 1))]))
            ? $this->mimeTypes[strtolower(substr($fileName, strrpos($fileName, '.') + 1))]
            : "application/octet-stream";
    }

    /**
     * Send the body as HTTP response
     *
     * @param bool $compress
     * @param bool $secure
     * @param int $compressLevel
     */
    public function send(bool $compress = false, bool $secure = true, int $compressLevel = 6): void
    {
        // cancel if already sent
        if ($this->isSent()) {
            return;
        }

        // security headers
        if ($secure) {
            $this->withSecurityHeaders();
        }

        // response body compression
        if ($compress) {
            $this->compress($compressLevel);
        }

        // enable cache
        if ($this->useCache) {
            $this->withCacheHeaders();
        }

        // apply headers & send
        $this->applyHeaders();
        $this->isSent = true;
        echo $this->body;
    }

    /**
     * Sends file-content as response
     *
     * @param string $fileName
     * @param bool $secure
     * @throws UnknownFileSentException
     */
    public function sendFile(string $fileName, bool $secure = true): void
    {
        // cancel if already sent
        if ($this->isSent()) {
            return;
        }

        // security headers
        if ($secure) {
            $this->withSecurityHeaders();
        }

        // enable cache
        if ($this->useCache) {
            $this->withCacheHeaders();
        }

        $basePath = str_replace('/', DIRECTORY_SEPARATOR, $this->storagePath);
        $fullPath = "$basePath/$fileName";
        $realPath = realpath($fullPath);

        if (strpos($fullPath, $basePath) !== 0) {
            throw new UnknownFileSentException($fileName);
        }
        if ($realPath === false || strpos($realPath, $basePath) !== 0) {
            throw new UnknownFileSentException($fileName);
        }
        if (!file_exists($fullPath)) {
            throw new UnknownFileSentException($fileName);
        }

        $type = $this->getMIMEType($fileName);

        $this->withHeader("Content-Type", $type);
        $this->withHeader("Content-Length", (string)filesize($fullPath));
        $this->withHeader("Content-Disposition", 'attachment; filename="' . $fileName . '"');

        $this->applyHeaders();

        $this->isSent = true;
        readfile($fullPath);
    }

    /**
     * Renders a view as response
     *
     * @param string $viewName
     * @param array|null $variables
     * @param string|null $override
     * @param bool $secure
     * @throws ViewNotFoundException
     */
    public function render(
        string $viewName,
        ?array $variables = null,
        ?string $override = null,
        bool $secure = true
    ): void {
        // security headers
        if ($secure) {
            $this->withSecurityHeaders();
        }

        // enable cache
        if ($this->useCache) {
            $this->withCacheHeaders();
        }

        $this->applyHeaders();

        $this->isSent = true;

        View::render($viewName, $variables, $override);
    }

    /**
     * Returns response in string format
     *
     * @return string
     */
    public function toString(): string
    {
        return "Response Object (cached=" . ($this->useCache) ? "yes" : "no" . ")";
    }
}

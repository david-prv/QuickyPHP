<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Middleware;

use Quicky\Core\Config;
use Quicky\Core\DynamicLoader;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Interfaces\MiddlewareInterface;
use Exception;

/**
 * Class LoggingMiddleware
 */
class LoggingMiddleware implements MiddlewareInterface
{
    /**
     * Error Level
     *
     * 0 = Info
     * 1 = Warning
     * 2 = Error
     *
     * @var int
     */
    private int $errorLevel = 0;

    /**
     * Log location
     *
     * @var string
     */
    private string $logPath;

    /**
     * Log templates
     */
    const INFO = "[Info] %s | %s" . PHP_EOL;
    const WARN = "[Warn] %s | %s" . PHP_EOL;
    const ERROR = "[Error] %s | %s" . PHP_EOL;

    /**
     * LoggingMiddleware constructor.
     *
     * @param string|null $logFile
     */
    public function __construct(?string $logFile = null)
    {
        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        $currentDate = date("d-m-Y", time());
        $this->logPath = $logFile ?? getcwd() . $config->getLogsPath() . "/quicky-log-$currentDate.log";
    }

    /**
     * Returns correct template
     *
     * @param int $level
     * @return string
     */
    private function getTemplate(int $level): string
    {
        return ($level === 0) ? self::INFO : (($level === 1) ? self::WARN : self::ERROR);
    }

    /**
     * Write log entry
     *
     * @param string $message
     * @param int|null $errLevel
     * @throws Exception
     */
    private function writeLog(string $message, ?int $errLevel = null): void
    {
        $level = $errLevel ?? $this->errorLevel;
        $file = $this->logPath;

        if (!file_exists($file)) {
            @fopen($file, "w");
        }

        $template = $this->getTemplate($level);

        try {
            file_put_contents($file, sprintf($template, date(DATE_ATOM), $message), FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $this->errorLevel(2);
            $template = $this->getTemplate(2);

            echo sprintf($template, date(DATE_ATOM), "Could not write log!");
        }
    }

    /**
     * Set permanent error level
     *
     * @param int $level
     */
    private function errorLevel(int $level): void
    {
        $this->errorLevel = $level;
    }

    /**
     * Run middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response|null
     * @throws Exception
     */
    public function run(Request $request, Response $response, callable $next): Response
    {
        $this->errorLevel(0);

        $this->writeLog(
            "(" . $request->getID() . ") " . $request->getMethod() . " "
            . $request->getUrl() . " from " . $request->getRemote()[0]
            . " | " . $request->getUserAgent() . " | " . json_encode($request->getData())
        );

        return $next($request, $response);
    }
}

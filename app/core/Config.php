<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\DispatchingInterface;
use App\Utils\ConfigParser;
use App\Utils\Exceptions\ConfigParserException;

/**
 * Class Config
 */
class Config implements DispatchingInterface
{
    /**
     * Project info
     *
     * @var array
     */
    private array $project;

    /**
     * Cache info
     *
     * @var array
     */
    private array $cache;

    /**
     * Storage path
     *
     * @var string
     */
    private string $storage;

    /**
     * Logs path
     *
     * @var string
     */
    private string $logs;

    /**
     * Views path
     *
     * @var string
     */
    private string $views;

    /**
     * Parser instance
     *
     * @var object|null
     */
    private ?object $parser;

    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->parser = DynamicLoader::getLoader()->getInstance(ConfigParser::class);
        $this->dispatching = array("config");
    }

    /**
     * Return config instance
     *
     * @return Config|object|null
     * @throws ConfigParserException
     */
    public static function config()
    {
        $instance = DynamicLoader::getLoader()->getInstance(Config::class);

        if ($instance instanceof Config) return $instance;
        else throw new ConfigParserException();
    }

    /**
     * Initialize configuration
     *
     * @param string $mode
     */
    public function init(string $mode)
    {
        $config = $this->parser->parse($mode);

        $this->cache = $config["cache"];
        $this->project = $config["project"];
        $this->storage = $config["storage"];
        $this->views = $config["views"];
        $this->logs = $config["logs"];
    }

    /**
     * Returns project info
     *
     * @return array
     */
    public function getProject(): array
    {
        return $this->project;
    }

    /**
     * Returns project name
     *
     * @return string
     */
    public function getName(): string
    {
        if (!isset($this->project["name"])) return "Quicky - PHP framework";
        return (string)$this->project["name"];
    }

    /**
     * Returns project author
     *
     * @return string
     */
    public function getAuthor(): string
    {
        if (!isset($this->project["author"])) return "David Dewes";
        return (string)$this->project["author"];
    }

    /**
     * Returns project version
     *
     * @return string
     */
    public function getVersion(): string
    {
        if (!isset($this->project["version"])) return "0.0.1";
        return (string)$this->project["version"];
    }

    /**
     * Returns project env
     *
     * @return string
     */
    public function getEnv(): string
    {
        if (!isset($this->project["env"])) return "development";
        return (string)$this->project["env"];
    }

    /**
     * Is project in production mode?
     *
     * @return bool
     */
    public function isProd(): bool
    {
        return $this->getEnv() === "production";
    }

    /**
     * Is project in dev mode?
     *
     * @return bool
     */
    public function isDev(): bool
    {
        return !$this->isProd();
    }

    /**
     * Returns cache info
     *
     * @return array
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * Is caching enabled?
     *
     * @return bool
     */
    public function isCacheActive(): bool
    {
        if (!isset($this->cache["enabled"])) return false;
        return (bool)$this->cache["enabled"];
    }

    /**
     * Returns expiration duration
     *
     * @return int
     */
    public function getCacheExpiration(): int
    {
        if (!isset($this->cache["expires"])) return -1;
        return (int)$this->cache["expires"];
    }

    /**
     * Returns storage path
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->storage;
    }

    /**
     * Returns views path
     *
     * @return string
     */
    public function getViewsPath(): string
    {
        return $this->views;
    }

    /**
     * Returns logs path
     *
     * @return string
     */
    public function getLogsPath(): string
    {
        return $this->logs;
    }

    /**
     * Checks if class is dispatching
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool
    {
        return in_array($method, $this->dispatching);
    }
}
<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Core;

use Quicky\App;
use Quicky\Interfaces\DispatchingInterface;
use Quicky\Utils\ConfigParser;
use Quicky\Utils\Exceptions\ConfigParserException;

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
     * (Router) Cache path
     *
     * @var string
     */
    private string $cacheLoc;

    /**
     * Storage path
     *
     * @var string
     */
    private string $storage;

    /**
     * logs path
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
     * Legacy Check
     *
     * @var bool
     */
    private bool $legacy;

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
        $this->cache = [];
        $this->cacheLoc = "";
        $this->project = [];
        $this->storage = "";
        $this->views = "";
        $this->logs = "";
        $this->legacy = true;
    }

    /**
     * Return config instance
     *
     * @return Config
     * @throws ConfigParserException
     */
    public static function config(): Config
    {
        $instance = DynamicLoader::getLoader()->getInstance(Config::class);

        if ($instance instanceof Config) {
            return $instance;
        } else {
            throw new ConfigParserException();
        }
    }

    /**
     * Initialize configuration
     *
     * @param string $mode
     */
    public function init(string $mode): void
    {
        $config = $this->parser->parse($mode);

        $this->cache = $config["cache"];
        $this->cache["enabled"] = (bool)filter_var($this->cache["enabled"], FILTER_VALIDATE_BOOLEAN);
        $this->cache["expires"] = (int)filter_var($this->cache["expires"], FILTER_VALIDATE_INT);

        $this->project = $config["project"];
        $this->storage = $config["storage"];
        $this->views = $config["views"];
        $this->logs = $config["logs"];

        $this->cacheLoc = $this->cache["path"];

        $this->legacy = (bool)filter_var($config["legacy-check"], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Override environment
     *
     * @param string $override
     */
    public function setEnv(string $override): void
    {
        if ($override === "") {
            return;
        }
        $this->project["env"] = $override;
    }

    /**
     * Returns project info
     *
     * @param string|null $field
     * @return mixed
     */
    public function getProject(?string $field = null)
    {
        return $field ? $this->project[$field] : $this->project;
    }

    /**
     * Returns project env
     *
     * @return string
     */
    public function getEnv(): string
    {
        if (!isset($this->project["env"])) {
            return App::__STATE_DEVELOPMENT;
        }
        return (string)$this->project["env"];
    }

    /**
     * Is project in production mode?
     *
     * @return bool
     */
    public function isProd(): bool
    {
        return $this->getEnv() === App::__STATE_PRODUCTION;
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
        if (!isset($this->cache["enabled"])) {
            return false;
        }
        return (bool)$this->cache["enabled"];
    }

    /**
     * Returns expiration duration
     *
     * @return int
     */
    public function getCacheExpiration(): int
    {
        if (!isset($this->cache["expires"])) {
            return -1;
        }
        return (int)$this->cache["expires"];
    }

    /**
     * Returns (router) cache path
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cacheLoc;
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
     * Returns whether legacy versions are checked
     *
     * @return bool
     */
    public function isLegacySensitive(): bool
    {
        return $this->legacy;
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

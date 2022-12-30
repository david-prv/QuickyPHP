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
 * Class Config
 */
class Config
{
    private array $project;
    private array $cache;
    private string $storage;
    private string $views;

    private ?object $parser;

    const LOAD_FROM_JSON = "json";
    const LOAD_FROM_ENV = "env";
    const LOAD_DEFAULT = "default";

    public function __construct()
    {
        $this->parser = DynamicLoader::getLoader()->getInstance(ConfigParser::class);
    }

    public function init(string $mode)
    {
        $config = ($this->parser instanceof ConfigParser)
            ? $this->parser->parse($mode)
            : array(
                "project" => array(
                    "name" => "Micro-Framework",
                    "author" => "David Dewes",
                    "version" => "0.0.1",
                    "env" => "development"
                ),
                "cache" => array(
                    "enabled"=> true,
                    "expires" => 3600
                ),
                "storage" => "/quicky/storage",
                "views" => "/quicky/views"
            );

        $this->cache = $config["cache"];
        $this->project = $config["project"];
        $this->storage = $config["storage"];
        $this->views = $config["views"];
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
     * Returns cache info
     *
     * @return array
     */
    public function getCache(): array
    {
        return $this->cache;
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
}
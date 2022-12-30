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
 * Class ConfigParser
 */
class ConfigParser
{
    public function parse($mode): array
    {
        switch ($mode) {
            case Config::LOAD_FROM_ENV:
                return $this->loadFromEnv();
            case Config::LOAD_FROM_JSON:
                return $this->loadFromJSON();
            case Config::LOAD_DEFAULT:
            default:
                return $this->loadDefault();
        }
    }

    public function loadFromJSON(): array
    {
        return array(); // TODO
    }

    public function loadFromEnv(): array
    {
        return array(); // TODO
    }

    public function loadDefault(): array
    {
        return array(
            "project" => array(
                "name" => "Quicky - PHP framework",
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
    }
}
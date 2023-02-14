<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Utils;

use Quicky\App;
use Quicky\Utils\Exceptions\ConfigParserException;

/**
 * Class ConfigParser
 */
class ConfigParser
{
    /**
     * Parses config
     *
     * @param $mode
     * @return array
     * @throws ConfigParserException
     */
    public function parse($mode): array
    {
        switch ($mode) {
            case App::QUICKY_CNF_MODE_ENV:
                return $this->loadFromEnv();
            case App::QUICKY_CNF_MODE_JSON:
                return $this->loadFromJSON();
            case App::QUICKY_CNF_MODE_DEFAULT:
            default:
                return $this->loadDefault();
        }
    }

    /**
     * Loads from JSON
     *
     * @return array
     * @throws ConfigParserException
     */
    public function loadFromJSON(): array
    {
        $expectedPath = getcwd() . "/Quicky/Config/config.json";
        if (is_file($expectedPath)) {
            $json = json_decode(file_get_contents($expectedPath), true);
            if (is_null($json) || $json === false) {
                throw new ConfigParserException();
            }

            return $json;
        } else {
            throw new ConfigParserException();
        }
    }

    /**
     * Loads from Env
     *
     * @return array
     */
    private function loadFromEnv(): array
    {
        return array(
            "project" => explode(",", getenv("PROJECT_DETAILS")),
            "cache" => explode(",", getenv("CACHE_INFO")),
            "storage" => getenv("STORAGE_PATH"),
            "views" => getenv("VIEWS_PATH"),
            "logs" => getenv("LOGS_PATH")
        );
    }

    /**
     * Loads default config
     *
     * @return array
     * @throws ConfigParserException
     */
    private function loadDefault(): array
    {
        $expectedPath = getcwd() . "/Quicky/Config/default.json";
        if (is_file($expectedPath)) {
            $json = json_decode(file_get_contents($expectedPath), true);
            if (is_null($json) || $json === false) {
                throw new ConfigParserException();
            }

            return $json;
        } else {
            throw new ConfigParserException();
        }
    }
}

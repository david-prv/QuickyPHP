<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Utils;

use App\Quicky;
use App\Utils\Exceptions\ConfigParserException;

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
            case Quicky::QUICKY_CNF_MODE_ENV:
                return $this->loadFromEnv();
            case Quicky::QUICKY_CNF_MODE_JSON:
                return $this->loadFromJSON();
            case Quicky::QUICKY_CNF_MODE_DEFAULT:
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
        $expectedPath = getcwd() . "/app/Config/config.json";
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
            "project" => getenv("project"),
            "cache" => getenv("cache"),
            "storage" => getenv("storage"),
            "views" => getenv("views"),
            "logs" => getenv("logs")
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
        $expectedPath = getcwd() . "/app/Config/default.json";
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

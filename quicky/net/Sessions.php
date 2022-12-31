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
 * Class Sessions
 */
class Sessions
{
    public static function session(): object
    {
        return DynamicLoader::getLoader()->getInstance(Sessions::class);
    }

    public function start(): void
    {

    }

    public function destroy(): void
    {

    }

    public function regenerate(): void
    {

    }

    public function set(string $name, string $value): void
    {

    }

    public function get(string $name): string
    {
        return "";
    }
}
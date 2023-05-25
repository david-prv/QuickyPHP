<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Interfaces;

/**
 * Interface ManagerInterface
 */
interface ManagerInterface
{
    /**
     * Get a variable
     *
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string;

    /**
     * Set a variable
     *
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void;

    /**
     * Set a range of variables
     *
     * @param array $assoc
     */
    public function setRange(array $assoc): void;

    /**
     * Unset a variable
     *
     * @param string $name
     */
    public function unset(string $name): void;
}

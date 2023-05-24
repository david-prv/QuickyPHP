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

use Quicky\Interfaces\DispatchingInterface;

/**
 * Class Aliases
 */
class Aliases implements DispatchingInterface
{
    /**
     * Array of all aliases
     * Mapped from Name to Master
     *
     * @var array
     */
    private array $aliases;

    /**
     * Dispatching methods
     *
     * @var array|string[]
     */
    private array $dispatching;

    /**
     * Aliases constructor.
     */
    public function __construct()
    {
        $this->aliases = array();
        $this->dispatching = array("alias");
    }

    /**
     * Creates a new alias
     *
     * Takes a "aliasName" string, which is the name of the alias,
     * like "sleep200", and the actual master function, which can be represented
     * by an anonymous function, a string like "sleep" or an array for
     * class-specific methods like "array('MyClass', 'methodName')".
     *
     * @param string $aliasName
     * @param mixed $masterFunction
     * @param bool $ignoreClasses
     */
    public function alias(string $aliasName, $masterFunction, bool $ignoreClasses = true): void
    {
        // pre-condition
        $type = gettype($masterFunction);
        if (($type !== "string" && $type !== "object" && $type !== "array")) {
            return;
        }

        // add alias
        if ($ignoreClasses === false && class_exists($masterFunction)) {
            class_alias($masterFunction, $aliasName);
        } else {
            $this->aliases[$aliasName] = $masterFunction;
        }
    }

    /**
     * Checks whether a methodName is used as alias
     *
     * @param string $methodName
     * @return bool
     */
    public function isAlias(string $methodName): bool
    {
        return isset($this->aliases[$methodName]);
    }

    /**
     * Evaluates alias' master function
     *
     * @param string $aliasName
     * @param mixed ...$args
     * @return mixed
     */
    public function evaluate(string $aliasName, ...$args)
    {
        if (isset($this->aliases[$aliasName])) {
            return call_user_func($this->aliases[$aliasName], ...$args);
        }
        return null;
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

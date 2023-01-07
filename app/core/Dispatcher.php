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

use App\Utils\Exceptions\UnknownCallException;

/**
 * Class Dispatcher
 */
class Dispatcher
{
    /**
     * Dispatches a method to the corresponding
     * dispatching class.
     *
     * @param string $name
     * @param array $args
     * @param string|null $className
     * @return mixed
     * @throws UnknownCallException
     */
    public static function dispatch(string $name, array $args, ?string $className = null)
    {
        $loader = DynamicLoader::getLoader();
        if (!is_null($className) && method_exists($className, $name)) {
            $c = $loader->getInstance($className);

            if (!self::canDispatchMethod($className, $name)) throw new UnknownCallException($name);
            if (is_null($c)) throw new UnknownCallException($name);
            else return call_user_func(array($c, $name), ...$args);
        } else {
            // the following method will use a MST (method search tree),
            // which is implemented as binary search tree. Because of that,
            // the running time for the first search is in average O(log n).
            // Because of the use of our cache, we even improve that running time
            // for every further call to constant time O(1).
            $className = $loader->findMethod($name);
            if (is_null($className)) throw new UnknownCallException($name);

            if (!self::canDispatchMethod($className, $name)) throw new UnknownCallException($name);

            if (is_null($className)) throw new UnknownCallException($name);
            else {
                $c = $loader->getInstance($className);
                if (is_null($c)) throw new UnknownCallException($name);
                return call_user_func(array($c, $name), ...$args);
            }
        }
    }

    /**
     * Checks whether a className has a
     * certain type (nomenclature: "[name][type]",
     * e.g. "ManagerInterface")
     *
     * @param string $className
     * @param string $classType
     * @return bool
     */
    public static function classIsTypeOf(string $className, string $classType): bool
    {
        $length = strlen($classType);
        if (!$length) return true;

        return substr($className, -$length) === $classType;
    }

    /**
     * Checks whether a class is an interface
     *
     * @param string $className
     * @return bool
     */
    public static function isInterface(string $className): bool
    {
        return self::classIsTypeOf($className, "Interface");
    }

    /**
     * Checks whether a class is an exception
     *
     * @param string $className
     * @return bool
     */
    public static function isException(string $className): bool
    {
        return self::classIsTypeOf($className, "Exception");
    }

    /**
     * Checks whether a class is dispatching
     *
     * @param string $className
     * @return bool
     */
    public static function canDispatch(string $className): bool
    {
        // skip interfaces & exceptions
        if (self::isInterface($className) || self::isException($className)) return false;
        return (method_exists($className, "dispatches"));
    }

    /**
     * Checks if a method is dispatched by
     * a certain class
     *
     * @param string $className
     * @param string $methodName
     * @return bool
     */
    public static function canDispatchMethod(string $className, string $methodName): bool
    {
        if (!self::canDispatch($className)) return false;

        // check instance
        $instance = DynamicLoader::getLoader()->getInstance($className);
        return $instance->dispatches($methodName);
    }
}
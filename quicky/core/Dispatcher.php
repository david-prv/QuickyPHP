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
     * @throws UnknownCallException
     * @throws ReflectionException
     */
    public static function dispatch(string $name, array $args, ?string $className = null)
    {
        $loader = DynamicLoader::getLoader();
        if (!is_null($className) && method_exists($className, $name)) {
            $c = $loader->getInstance($className);

            if (!Dispatcher::canDispatchMethod($className, $name)) throw new UnknownCallException($name);
            if (is_null($c)) throw new UnknownCallException($name);
            else call_user_func(array($c, $name), ...$args);
        } else {
            $cname = $loader->findMethod($name);

            if (is_null($cname)) throw new UnknownCallException($name);
            else {
                $c = $loader->getInstance($cname);
                if (is_null($c)) throw new UnknownCallException($name);
                call_user_func(array($c, $name), ...$args);
            }
        }
    }

    /**
     * Check if a class is dispatching
     *
     * @param string $className
     * @return bool
     * @throws ReflectionException
     */
    public static function isDispatchingClass(string $className)
    {
        $reflection = new ReflectionClass($className);
        return strpos($reflection->getDocComment(), "@dispatch") !== false;
    }

    /**
     * Checks if a method is dispatched by
     * a certain class
     *
     * @param string $className
     * @param string $methodName
     * @return bool
     * @throws ReflectionException
     */
    public static function canDispatchMethod(string $className, string $methodName)
    {
        $reflection = new ReflectionClass($className);
        return strpos($reflection->getDocComment(), "@dispatch $methodName") !== false;
    }
}
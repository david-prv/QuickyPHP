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
     * @return mixed
     */
    public static function dispatch(string $name, array $args, ?string $className = null)
    {
        $loader = DynamicLoader::getLoader();
        if (!is_null($className) && method_exists($className, $name)) {
            $c = $loader->getInstance($className);

            if (!Dispatcher::canDispatchMethod($className, $name)) throw new UnknownCallException($name);
            if (is_null($c)) throw new UnknownCallException($name);
            else return call_user_func(array($c, $name), ...$args);
        } else {
            $cname = $loader->findMethod($name);

            if (is_null($cname)) throw new UnknownCallException($name);
            else {
                $c = $loader->getInstance($cname);
                if (is_null($c)) throw new UnknownCallException($name);
                return call_user_func(array($c, $name), ...$args);
            }
        }
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
        // skip interfaces
        if ($className[0] === "I") return false;

        // check instance
        $instance = DynamicLoader::getLoader()->getInstance($className);
        if (method_exists($className, "dispatches")) {
            return $instance->dispatches($methodName);
        } else return false;
    }
}
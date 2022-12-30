<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

class Dispatcher
{
    /**
     * @param string $name
     * @param array $args
     * @param string|null $className
     * @throws UnknownCallException
     */
    public static function dispatch(string $name, array $args, ?string $className = null)
    {
        $loader = DynamicLoader::getLoader();
        $name = "__$name"; // only methods with prefix allowed
        if (!is_null($className) && method_exists($className, $name)) {
            $c = $loader->getInstance($className);

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
}
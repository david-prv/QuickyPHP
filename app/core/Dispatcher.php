<?php

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
        if (!is_null($className) && method_exists($className, $name)) {
            $c = $loader->getInstance($className);

            if (is_null($c))
                throw new UnknownCallException("$name is not a method or not instantiable");
            else
                call_user_func(array($c, $name), ...$args);
        } else {
            $c = $loader->findMethod($name);
            $i = $loader->getInstance($c);

            if (is_null($c) || is_null($i))
                throw new UnknownCallException("$name is not a method or not instantiable");
            else
                call_user_func(array($i, $name), ...$args);
        }
    }
}
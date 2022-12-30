<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class App
 *
 * @method static get(string $pattern, callable $callback)
 * @method static post(string $pattern, callable $callback)
 */
class App
{
    private static ?App $instance = null;

    /**
     * App constructor.
     */
    private function __construct()
    {
    }

    /**
     * Creates or returns an instance
     *
     * @return App|null
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    /**
     * Run application
     *
     * @throws UnknownRouteException
     */
    public function run()
    {
        $router = DynamicLoader::getLoader()->getInstance(Router::class);
        if ($router instanceof Router) $router(new Request(), new Response());
    }

    /**
     * Handle static function calls.
     * They will be dispatched to their corresponding
     * dispatching classes.
     *
     * @param $name
     * @param $arguments
     * @throws UnknownCallException
     * @throws ReflectionException
     */
    public static function __callStatic($name, $arguments): void
    {
        Dispatcher::dispatch($name, $arguments);
    }
}

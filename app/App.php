<?php

declare(strict_types=1);

/**
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
     */
    public function run()
    {
        $router = DynamicLoader::getLoader()->getInstance("Router");
        $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    }

    /**
     * @param $name
     * @param $arguments
     * @throws UnknownCallException
     */
    public static function __callStatic($name, $arguments): void
    {
        Dispatcher::dispatch($name, $arguments);
    }
}

<?php

declare(strict_types=1);

use app\core\Dispatcher;
use app\utils\exceptions\UnknownCallException;

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
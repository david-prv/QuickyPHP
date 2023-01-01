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
 * Class Quicky
 *
 * Routes:
 * @method static get(string $pattern, callable $callback, ...$middleware)
 * @method static post(string $pattern, callable $callback, ...$middleware)
 * @method static put(string $pattern, callable $callback, ...$middleware)
 * @method static update(string $pattern, callable $callback, ...$middleware)
 * @method static delete(string $pattern, callable $callback, ...$middleware)
 * @method static patch(string $pattern, callable $callback, ...$middleware)
 *
 * Views:
 * @method static view()
 * @method static render(string $viewName, ?array $params = null)
 *
 * Session:
 * @method static session()
 *
 * Config:
 * @method static config()
 *
 * Router:
 * @method static router()
 */
class Quicky
{
    /**
     * Quicky instance
     *
     * @var Quicky|null
     */
    private static ?Quicky $instance = null;

    /**
     * Project env
     *
     * @var string
     */
    private string $env;

    /**
     * Quicky constructor.
     *
     * @param string $mode
     */
    private function __construct(string $mode)
    {
        DynamicLoader::getLoader()->registerInstance(Quicky::class, $this);

        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        if (!is_null($config) && $config instanceof Config) {
            $config->init($mode);
            $this->env = $config->getEnv();
        } else {
            $this->stop(1);
        }
    }

    /**
     * Creates or returns an instance
     *
     * @param string $mode
     * @return Quicky
     */
    public static function create(string $mode = Config::LOAD_DEFAULT)
    {
        if (self::$instance === null) {
            self::$instance = new Quicky($mode);
        }
        return self::$instance;
    }

    /**
     * Run application
     *
     * @throws UnknownRouteException
     */
    public function run(): void
    {
        $router = DynamicLoader::getLoader()->getInstance(Router::class);
        if ($router instanceof Router) $router(new Request(), new Response());
        else $this->stop(1);
    }

    /**
     * Stop application
     *
     * @param int $code
     */
    public function stop(int $code = 0): void
    {
        exit($code);
    }

    /**
     * Handle static function calls.
     * They will be dispatched to their corresponding
     * dispatching classes.
     *
     * @param $name
     * @param $arguments
     * @throws UnknownCallException
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return Dispatcher::dispatch($name, $arguments);
    }
}

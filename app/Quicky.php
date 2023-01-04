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
 * Magic methods that should be known available by any
 * IDE for Quicky. Since they all should be called as statics, they
 * all are annotated as static, even though the dispatched methods
 * may be dynamic.
 *
 * Read more:
 * @link https://pear.php.net/package/PhpDocumentor/docs/latest/phpDocumentor/tutorial_tags.method.pkg.html
 *
 * Routes:
 * @method static void route(string $method, string $pattern, callable $callback, bool $passThrough = false, ...$middleware)
 * @method static void pass(string $method, string $pattern)
 *
 * Views:
 * @method static View view()
 * @method static void render(string $viewName, ?array $params = null)
 *
 * SessionManager:
 * @method static SessionManager session()
 *
 * CookieManager:
 * @method static CookieManager cookies()
 *
 * Config:
 * @method static Config config()
 *
 * Router:
 * @method static Router router()
 * @method static void useMiddleware(...$middleware)
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
     * Project config
     *
     * @var Config
     */
    private Config $config;

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
            $this->config = $config;
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
    public static function create(string $mode = Config::LOAD_DEFAULT): Quicky
    {
        if (self::$instance === null) {
            self::$instance = new Quicky($mode);
        }
        return self::$instance;
    }

    /**
     * Override/Set error handler
     *
     * @param callable|null $errorHandler
     * @param callable|null $exceptionHandler
     */
    public static function useHandlers(?callable $errorHandler = null, ?callable $exceptionHandler = null): void
    {
        if (!is_null($errorHandler)) set_error_handler($errorHandler);
        if (!is_null($exceptionHandler)) set_exception_handler($exceptionHandler);
    }

    /**
     * Run application
     *
     * @param bool $catchAllErrors
     */
    public function run(bool $catchAllErrors = false): void
    {
        // enable error catching for production or
        // iff parameter is set
        if ($this->config->isProd() || $catchAllErrors) {
            set_error_handler(function (string $errNo, string $errStr) {
                return $this->catchError($errNo, $errStr);
            });
            set_exception_handler(function (Throwable $e) {
                return $this->catchException($e);
            });
        }

        try {
            // route request here
            $router = DynamicLoader::getLoader()->getInstance(Router::class);

            if ($router instanceof Router) {
                $router(new Request(), new Response());
            } else $this->stop(1);

        } catch (UnknownRouteException $e) {
            $this->catchException($e);
        }
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
     * Basic error handler for production.
     * Catches all types of errors.
     *
     * @param string $errNo
     * @param string $errStr
     * @return callable|null ?callable
     */
    private function catchError(string $errNo, string $errStr): ?callable
    {
        View::error($errNo, $errStr);
        return null;
    }

    /**
     * Basic exception handler for production.
     * Catches all types of exceptions.
     *
     * @param Throwable $e
     * @return callable|null ?callable
     */
    private function catchException(Throwable $e): ?callable
    {
        View::except($e->getMessage());
        return null;
    }

    /**
     * Handle static function calls.
     * They will be dispatched to their corresponding
     * dispatching classes.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws UnknownCallException
     */
    public static function __callStatic($name, $arguments)
    {
        return Dispatcher::dispatch($name, $arguments);
    }
}

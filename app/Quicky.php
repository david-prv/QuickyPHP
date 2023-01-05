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
 * @method static void route(string $method, string $pattern, callable $callback, ...$middleware)
 * @method static void pass(string $pattern)
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
     * Handler types
     */
    const QUICKY_EXCEPTION_HANDLER = "exception";
    const QUICKY_ERROR_HANDLER = "error";

    /**
     * Config loading modes
     */
    const QUICKY_CNF_JSON = "json";
    const QUICKY_CNF_ENV = "env";
    const QUICKY_CNF_DEFAULT = "default";

    /**
     * Session fields
     */
    const QUICKY_SESSION_ID = "quicky_session_id";
    const QUICKY_SESSION_CREATED_AT = "quicky_created_at";
    const QUICKY_CSRF_TOKEN = "csrf_token";

    /**
     * Quicky constructor.
     *
     * @param string $mode
     * @param bool $catchErrors
     */
    private function __construct(string $mode, bool $catchErrors)
    {
        DynamicLoader::getLoader()->registerInstance(Quicky::class, $this);

        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        if (!is_null($config) && $config instanceof Config) {
            $config->init($mode);
            $this->config = $config;

            // enable error catching for production or
            // iff parameter is set
            if ($this->config->isProd() || $catchErrors) {
                set_error_handler(function (string $error_level, string $error_message, string $error_file, string $error_line) {
                    return $this->catchError($error_level, $error_message, $error_file, $error_line);
                });
                set_exception_handler(function (Throwable $e) {
                    return $this->catchException($e);
                });
            }
        } else {
            $this->stop(1);
        }
    }

    /**
     * Creates or returns an instance
     *
     * @param string $mode
     * @param bool $catchErrors
     * @return Quicky
     */
    public static function create(string $mode = Quicky::QUICKY_CNF_DEFAULT, bool $catchErrors = false): Quicky
    {
        if (self::$instance === null) {
            self::$instance = new Quicky($mode, $catchErrors);
        }
        return self::$instance;
    }

    /**
     * Override/Set error handlers
     *
     * @param string $type
     * @param callable $handler
     */
    public static function useHandler(string $type, callable $handler): void
    {
        switch ($type) {
            case self::QUICKY_ERROR_HANDLER:
                set_error_handler($handler);
                break;
            case self::QUICKY_EXCEPTION_HANDLER:
                set_exception_handler($handler);
                break;
            default:
                break;
        }
    }

    /**
     * Run application
     */
    public function run(): void
    {
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
     * @param string $error_level
     * @param string $error_message
     * @param string $error_file
     * @param string $error_line
     * @return callable|null ?callable
     */
    private function catchError(string $error_level, string $error_message, string $error_file,
                                string $error_line): ?callable
    {
        View::error($error_level, $error_message, $error_file, $error_line);
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

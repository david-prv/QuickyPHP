<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App;

use App\Core\Config;
use App\Core\Dispatcher;
use App\Core\DynamicLoader;
use App\Core\Managers\CookieManager;
use App\Core\Managers\SessionManager;
use App\Core\View;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Utils\Exceptions\UnknownCallException;
use App\Utils\Exceptions\UnknownRouteException;
use Throwable;

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
     * Config loading modes
     */
    const QUICKY_CNF_MODE_JSON = "json";
    const QUICKY_CNF_MODE_ENV = "env";
    const QUICKY_CNF_MODE_DEFAULT = "default";

    /**
     * In-built session fields
     */
    const QUICKY_SESSION_FIELD_ID = "quicky_session_id";
    const QUICKY_SESSION_FIELD_CREATED_AT = "quicky_created_at";
    const QUICKY_SESSION_FIELD_CSRF_TOKEN = "csrf_token";

    /**
     * Quicky constructor.
     *
     * @param bool $catchErrors
     * @param string $mode
     */
    private function __construct(bool $catchErrors, string $mode)
    {
        DynamicLoader::getLoader()->registerInstance(Quicky::class, $this);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);

        if ($mode === "") $mode = "default";

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
     * @param bool $catchErrors
     * @param string $mode
     * @return Quicky
     */
    public static function create(bool $catchErrors = false, string $mode = ""): Quicky
    {
        if (self::$instance === null) {
            self::$instance = new Quicky($catchErrors, $mode);
        }
        return self::$instance;
    }

    /**
     * Use custom settings
     *
     * @param array $settings
     */
    public static function use(array $settings): void
    {
        if (isset($settings["handlers"]) && isset($settings["handlers"]["error"])) {
            set_error_handler($settings["handlers"]["error"]);
        }
        if (isset($settings["handlers"]) && isset($settings["handlers"]["exception"])) {
            set_exception_handler($settings["handlers"]["exception"]);
        }
        if (isset($settings["middleware"])) {
            $router = DynamicLoader::getLoader()->getInstance(Router::class);
            if ($router instanceof Router) {
                $router->useMiddleware(...$settings["middleware"]);
            }
        }
        if (isset($settings["env"])) {
            $config = DynamicLoader::getLoader()->getInstance(Config::class);
            if ($config instanceof Config) {
                $config->setEnv($settings["env"]);
            }
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

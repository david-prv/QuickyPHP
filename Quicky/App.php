<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky;

use Quicky\Core\Aliases;
use Quicky\Core\Config;
use Quicky\Core\Dispatcher;
use Quicky\Core\DynamicLoader;
use Quicky\Core\Managers\CookieManager;
use Quicky\Core\Managers\SessionManager;
use Quicky\Core\View;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Http\Router;
use Quicky\Utils\Exceptions\NotAResponseException;
use Quicky\Utils\Exceptions\UnknownCallException;
use Quicky\Utils\Exceptions\UnknownRouteException;
use Quicky\Utils\Exceptions\ViewNotFoundException;
use Throwable;

/**
 * Class App
 *
 * Magic methods that should be known available by any
 * IDE for App. Since they all should be called as statics, they
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
 *
 * Aliases:
 * @method static void alias(string $aliasName, ...$masterFunction)
 */
class App
{
    /**
     * App instance
     *
     * @var App|null
     */
    private static ?App $instance = null;

    /**
     * Project config
     *
     * @var Config
     */
    private Config $config;

    /**
     * Currently handled request
     *
     * @var Request
     */
    private Request $request;

    /**
     * Currently prepared response
     *
     * @var Response|null
     */
    private ?Response $response = null;

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
     * App constructor.
     *
     * @param bool $catchErrors
     * @param string $mode
     */
    private function __construct(bool $catchErrors, string $mode)
    {
        $this->request = new Request();

        DynamicLoader::getLoader()->registerInstance(App::class, $this);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);

        if ($mode === "") {
            $mode = "default";
        }

        if (!is_null($config) && $config instanceof Config) {
            $config->init($mode);
            $this->config = $config;

            // enable error catching for production or
            // iff parameter is set
            if ($this->config->isProd() || $catchErrors) {
                set_error_handler(function (
                    string $errorLevel,
                    string $errorMessage,
                    string $errorFile,
                    string $errorLine
                ) {
                    return $this->catchError($errorLevel, $errorMessage, $errorFile, $errorLine);
                }, E_ALL);
                set_exception_handler(function (Throwable $e) {
                    return $this->catchException($e);
                });
            }
        } else {
            $this->stop();
        }
    }

    /**
     * Creates or returns an instance
     *
     * @param bool $catchErrors
     * @param string $mode
     * @return App
     */
    public static function create(bool $catchErrors = false, string $mode = ""): App
    {
        if (self::$instance === null) {
            self::$instance = new App($catchErrors, $mode);
        }
        return self::$instance;
    }

    /**
     * Applies error/exception handlers
     *
     * @param array $settings
     */
    private function useHandlers(array $settings): void
    {
        if (isset($settings["handlers"]) && isset($settings["handlers"]["error"])) {
            set_error_handler($settings["handlers"]["error"], E_ALL);
        }
        if (isset($settings["handlers"]) && isset($settings["handlers"]["exception"])) {
            set_exception_handler($settings["handlers"]["exception"]);
        }
    }

    /**
     * Applies globally used middleware
     *
     * @param array $settings
     */
    private function useMiddleware(array $settings): void
    {
        if (isset($settings["middleware"])) {
            $router = DynamicLoader::getLoader()->getInstance(Router::class);
            if ($router instanceof Router) {
                $router->useMiddleware(...$settings["middleware"]);
            }
        }
    }

    /**
     * Applies new environment state
     *
     * @param array $settings
     */
    private function useEnv(array $settings): void
    {
        if (isset($settings["env"])) {
            $config = DynamicLoader::getLoader()->getInstance(Config::class);
            if ($config instanceof Config) {
                $config->setEnv($settings["env"]);
            }
        }
    }

    /**
     * Applies globally applied placeholders
     *
     * @param array $settings
     */
    private function usePlaceholders(array $settings): void
    {
        if (isset($settings["placeholders"])) {
            $view = DynamicLoader::getLoader()->getInstance(View::class);
            if ($view instanceof View) {
                $view->usePlaceholders($settings["placeholders"]);
            }
        }
    }

    /**
     * Use custom settings
     *
     * @param array $settings
     */
    public static function use(array $settings): void
    {
        $quicky = DynamicLoader::getLoader()->getInstance(App::class);

        $quicky->useHandlers($settings);
        $quicky->useMiddleware($settings);
        $quicky->useEnv($settings);
        $quicky->usePlaceholders($settings);
    }

    /**
     * Starts the application
     *
     * @throws UnknownRouteException
     * @throws NotAResponseException
     */
    public function run(): void
    {
        // route request here
        $router = DynamicLoader::getLoader()->getInstance(Router::class);

        if ($router instanceof Router) {
            $router($this->request, $this->response = new Response());
        } else {
            $this->stop();
        }
    }

    /**
     * Stops the application abruptly
     */
    public function stop(): void
    {
        exit();
    }

    /**
     * Halts the application with proper
     * response
     *
     * @param int $code
     * @param string $message
     */
    public function halt(int $code = 200, string $message = ""): void
    {
        $response = new Response();
        $response->status($code);
        $response->write($message);
        exit();
    }

    /**
     * Basic error handler for production.
     * Catches all types of errors.
     *
     * @param string $errorLevel
     * @param string $errorMessage
     * @param string $errorFile
     * @param string $errorLine
     * @return callable|null ?callable
     * @throws ViewNotFoundException
     */
    private function catchError(
        string $errorLevel,
        string $errorMessage,
        string $errorFile,
        string $errorLine
    ): ?callable {
        View::error($errorLevel, $errorMessage, $errorFile, $errorLine, $this->request);
        return null;
   }

    /**
     * Basic exception handler for production.
     * Catches all types of exceptions.
     *
     * @param Throwable $e
     * @return callable|null ?callable
     * @throws ViewNotFoundException
     */
    private function catchException(Throwable $e): ?callable
    {
        View::except($e->getMessage(), $this->request);
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

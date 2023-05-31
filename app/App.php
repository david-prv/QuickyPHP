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
 * @method static void group($predicate, callable $definitions)
 *
 * Aliases:
 * @method static void alias(string $aliasName, mixed $masterFunction, bool $ignoreClasses = true)
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
    private ?Response $response;

    /**
     * Is the application already running?
     *
     * @var bool
     */
    public static bool $running = false;

    /**
     * In-built session fields
     */
    const __SESSION_ID = "quicky_session_id";
    const __SESSION_CREATED_AT = "quicky_created_at";
    const __SESSION_CSRF = "csrf_token";

    /**
     * In-built identifiers
     */
    const __EVENT_ERROR = "error";
    const __EVENT_EXCEPTION = "exception";
    const __STATE_PRODUCTION = "production";
    const __STATE_DEVELOPMENT = "development";
    const __MODE_ENV = "env";
    const __MODE_JSON = "json";
    const __MODE_DEFAULT = "default";

    /**
     * App constructor.
     *
     * @param bool $catchErrors
     * @param string $mode
     */
    private function __construct(bool $catchErrors = false, string $mode = "")
    {
        // register instance such that DynamicLoader is unlocked
        DynamicLoader::getLoader()->registerInstance(App::class, $this);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        $this->request = new Request();
        $this->response = null;

        if (!version_compare(phpversion(), "7.4.0", "ge")) {
            $this->halt(400);
        }

        if ($mode === "") {
            $mode = "default";
        }

        if ($config instanceof Config) {
            $config->init($mode);
            $this->config = $config;

            // enable error catching for production or
            // iff parameter is set
            if ($this->config->isProd() || $catchErrors) {
                set_error_handler(function (
                    int    $errorLevel,
                    string $errorMessage,
                    string $errorFile,
                    int    $errorLine
                ) {
                    return $this->catchError("$errorLevel", $errorMessage, $errorFile, "$errorLine");
                }, E_ALL);
                set_exception_handler(function (Throwable $e) {
                    return $this->catchException($e);
                });
            }

            if ($config->isLegacySensitive()) {
                $this->checkForLegacy();
            }
        } else {
            $this->stop();
        }
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
     * Checks for older PHP versions and triggers
     * a warning (which is by default displayed in the standard view)
     */
    private function checkForLegacy(): void
    {
        if (version_compare(phpversion(), "8.0.0", "lt")) {
            trigger_error("Your PHP version is very old", E_USER_NOTICE);
        }
    }

    /**
     * Applies error/exception handlers
     *
     * @param array $settings
     */
    private function applyHandlers(array $settings): void
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
    private function applyMiddleware(array $settings): void
    {
        if (isset($settings["middleware"])) {
            $router = DynamicLoader::getLoader()->getInstance(Router::class);
            if ($router instanceof Router) {
                if (is_array($settings["middleware"])) {
                    $router->useMiddleware(...$settings["middleware"]);
                } else {
                    $router->useMiddleware($settings["middleware"]);
                }
            }
        }
    }

    /**
     * Applies new environment state
     *
     * @param array $settings
     */
    private function applyEnv(array $settings): void
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
    private function applyPlaceholders(array $settings): void
    {
        if (isset($settings["placeholders"])) {
            $view = DynamicLoader::getLoader()->getInstance(View::class);
            if ($view instanceof View && gettype($settings["placeholders"]) === "array") {
                $view->usePlaceholders($settings["placeholders"]);
            }
        }
    }

    /**
     * Applies aliases
     *
     * @param array $settings
     * @return void
     */
    private function applyAlias(array $settings): void
    {
        $aliases = DynamicLoader::getLoader()->getInstance(Aliases::class);
        if ($aliases instanceof Aliases && isset($settings["alias"]) && gettype($settings["alias"]) === "array"
            && count($settings["alias"]) >= 2) {
            if (gettype($settings["alias"][0]) === "array") {
                foreach ($settings["alias"] as $alias) {
                    $aliases->alias(...$alias);
                }
                return;
            }
            $aliases->alias(...$settings["alias"]);
        }
    }

    /**
     * Use custom settings
     *
     * @param array $settings
     */
    public static function use(...$settings): void
    {
        if (self::$running) {
            return;
        }

        // decide the format of settings: many or single
        $userSettings = $settings[0];
        if (count($settings) >= 2) {
            $userSettings = [$settings[0] => $settings[1]];
        }

        $app = DynamicLoader::getLoader()->getInstance(App::class);

        // instruct the application to apply the settings
        $app->applyHandlers($userSettings);
        $app->applyMiddleware($userSettings);
        $app->applyEnv($userSettings);
        $app->applyPlaceholders($userSettings);
        $app->applyAlias($userSettings);
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
        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        $router = DynamicLoader::getLoader()->getInstance(
            Router::class,
            [$config->getCachePath()]
        );

        if ($router instanceof Router) {
            self::$running = true;
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
        self::$running = false;
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
        self::$running = false;
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
     */
    private function catchException(Throwable $e): ?callable
    {
        View::except($e->getMessage(), $this->request);
        return null;
    }
}

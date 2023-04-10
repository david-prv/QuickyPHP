<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky;

use Quicky\Interfaces\DispatchingInterface;
use Quicky\Interfaces\MiddlewareInterface;

/**
 * Class AppFactory
 */
class AppFactory implements DispatchingInterface
{
    /**
     * Factory instance
     *
     * @var AppFactory|null
     */
    private static ?AppFactory $instance = null;

    /**
     * Application instance
     *
     * @var App|null
     */
    private ?App $appInstance = null;

    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Middleware used for the framework
     *
     * @var array
     */
    private array $middleware;

    /**
     * Globally applied placeholders
     * for the QuickyPHP views
     *
     * @var array
     */
    private array $globalPlaceholders;

    /**
     * Error Handler used for the framework
     *
     * @var object|null
     */
    private ?object $errorHandler = null;

    /**
     * Exception Handler used for the framework
     *
     * @var object|null
     */
    private ?object $exceptionHandler = null;

    /**
     * The state in which the application
     * will run later-on
     *
     * @var string
     */
    private string $envState;

    /**
     * If set to true: Sets the default error handlers
     * to avoid crashes during development and clearer
     * feedback of what happened
     *
     * @var bool
     */
    private bool $catchErrors;

    /**
     * AppFactory constructor.
     */
    private function __construct()
    {
        $this->envState = "production";
        $this->catchErrors = false;
        $this->middleware = array();
        $this->dispatching = array("factory");
    }

    /**
     * Returns the factory
     *
     * @return AppFactory
     */
    public static function factory(): AppFactory
    {
        if (self::$instance === null) {
            self::$instance = new AppFactory();
        }
        return self::$instance;
    }

    /**
     * Build the final application
     *
     * @return App
     */
    public function build(): App
    {
        if ($this->appInstance === null) {
            $this->appInstance = App::create($this->catchErrors);
        }
        return $this->appInstance;
    }

    /**
     * Use middleware that runs before the actual
     * HTTP response of QuickyPHP
     *
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function withMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * If used: Enforces the application to use the default development
     * error/exception handlers, no matter if this app runs in production or not.
     * This handlers are usually only applied in development.
     *
     * @return $this
     */
    public function enforceCatchError(): self
    {
        $this->catchErrors = true;
        return $this;
    }

    /**
     * Use a custom error handler
     *
     * @param callable|null $callback
     * @return $this
     */
    public function withErrorHandler(?callable $callback): self
    {
        $this->errorHandler = $callback;
        return $this;
    }

    /**
     * Use a custom exception handler
     *
     * @param callable|null $callback
     * @return $this
     */
    public function withExceptionHandler(?callable $callback): self
    {
        $this->exceptionHandler = $callback;
        return $this;
    }

    /**
     * In which state is this application?
     * - production
     * - development
     *
     * @param string $state
     * @return $this
     */
    public function withEnvState(string $state): self
    {
        $this->envState = ($state === "production") ? $state : "development";
        return $this;
    }

    /**
     * Use placeholders, which are applied in ALL views,
     * delivered by this application
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function withGlobalPlaceholder(string $name, string $value): self
    {
        $this->globalPlaceholders[] = [$name => $value];
        return $this;
    }

    /**
     * Add a function alias, s.t. App::yourFuncName()
     * runs the defined behaviour / master function
     *
     * @param string $aliasName
     * @param $masterFunction
     * @return $this
     */
    public function withFunctionAlias(string $aliasName, $masterFunction): self
    {
        App::alias($aliasName, $masterFunction);
        return $this;
    }

    /**
     * Alias / Rename an existing class.
     * It is also possible to rename the application
     * itself. Please use fully qualified class names!
     *
     * @param string $aliasName
     * @param string $masterClass
     * @return $this
     */
    public function withClassAlias(string $aliasName, string $masterClass): self
    {
        App::alias($aliasName, $masterClass, false);
        return $this;
    }

    /**
     * Checks if class is dispatching
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool
    {
        return in_array($method, $this->dispatching);
    }
}
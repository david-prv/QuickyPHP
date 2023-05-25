<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky;

use Quicky\Core\DynamicLoader;

/**
 * Class AppFactory
 */
class AppFactory
{
    /**
     * App Factory instance
     *
     * @var AppFactory|null
     */
    private static ?AppFactory $instance = null;

    /**
     * The settings array
     *
     * @var array
     */
    private array $settings;

    /**
     * All planned middlewares before
     * application initialization
     *
     * @var array
     */
    private array $plannedMiddleware;

    /**
     * The used parsing method for
     * the config object
     *
     * @var string
     */
    private string $usedConfigMode;

    /**
     * Enforces usage of default error
     * handlers, even if application is NOT in prod
     *
     * @var bool
     */
    private bool $enforceCatchErrors;

    /**
     * AppFactory constructor.
     *
     * @param array|null $template
     */
    private function __construct(?array $template)
    {
        $this->settings = $template ?? [];
        $this->plannedMiddleware = [];
        $this->usedConfigMode = "";
        $this->enforceCatchErrors = false;
    }

    /**
     * Initialize an empty App Factory
     *
     * @return AppFactory
     */
    public static function empty(): AppFactory
    {
        if (self::$instance === null) {
            self::$instance = new AppFactory(null);
        }
        return self::$instance;
    }

    /**
     * Initialize a templated App Factory
     *
     * @param array $template
     * @return AppFactory
     */
    public static function template(array $template): AppFactory
    {
        if (self::$instance === null) {
            self::$instance = new AppFactory($template);
        }
        return self::$instance;
    }

    /**
     * Build the application
     *
     * @return App
     */
    public function build(): App
    {
        // create application instance
        $app = App::create($this->enforceCatchErrors, $this->usedConfigMode);

        // get middleware instances and apply them
        foreach ($this->plannedMiddleware as $className => $args) {
            App::use("middleware", DynamicLoader::getLoader()->getInstance($className, $args));
        }

        // apply settings & return
        App::use($this->settings);
        return $app;
    }

    /**
     * Define application state.
     * Optionally, you can pass an override to
     * the application, which enforces the framework
     * to catch errors, no matter what state was given.
     *
     * @param string $state
     * @param bool $ignoreStateAndCatch
     * @return $this
     */
    public function state(string $state, bool $ignoreStateAndCatch = false): self
    {
        if ($state !== App::__STATE_PRODUCTION && $state !== App::__STATE_DEVELOPMENT) {
            $state = "production";
        }
        $this->enforceCatchErrors = $ignoreStateAndCatch;
        $this->settings["env"] = $state;

        return $this;
    }

    /**
     * Define globally used middleware for
     * all later defined routes
     *
     * @param string $className
     * @param mixed ...$args
     * @return $this
     */
    public function middleware(string $className, ...$args): self
    {
        $this->plannedMiddleware[$className] = $args;
        return $this;
    }

    /**
     * Add an event listener to the application.
     * Events can be, for example: an error (default) or an
     * exception occurring, and so on...
     *
     * @param string|null $eventType
     * @param callable $eventCallback
     * @return $this
     */
    public function catch(?string $eventType, callable $eventCallback): self
    {
        switch ($eventType) {
            case App::__EVENT_EXCEPTION:
                $this->settings["handlers"]["exception"] = $eventCallback;
                break;
            default:
            case App::__EVENT_ERROR:
                $this->settings["handlers"]["error"] = $eventCallback;
                break;
        }
        return $this;
    }

    /**
     * Add an alias (function or class):
     * Use $ignoreClasses = false in case you want to address
     * a class instead of a function
     *
     * @param string $aliasName
     * @param mixed $masterFunction
     * @param bool $ignoreClasses
     * @return $this
     */
    public function alias(string $aliasName, $masterFunction, bool $ignoreClasses = true): self
    {
        $this->settings["alias"][] = [$aliasName, $masterFunction, $ignoreClasses];
        return $this;
    }

    /**
     * Update the used parsing mode
     * for configuration object.
     *
     * @param string $configMode
     * @return $this
     */
    public function loadConfig(string $configMode): self
    {
        $this->usedConfigMode = $configMode;
        return $this;
    }

    /**
     * Define globally applied placeholders for
     * all defined views
     *
     * @param array $placeholders
     * @return $this
     */
    public function placeholders(array $placeholders): self
    {
        $this->settings["placeholders"] = $placeholders;
        return $this;
    }
}

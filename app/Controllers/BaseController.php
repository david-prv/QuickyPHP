<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky\Controllers;

use Quicky\App;
use Quicky\Core\Config;
use Quicky\Core\DynamicLoader;
use Quicky\Interfaces\ControllerInterface;

/**
 * Class BaseController
 */
class BaseController implements ControllerInterface
{
    /**
     * Configuration reference
     *
     * @var Config
     */
    protected Config $config;

    /**
     * DynamicLoader reference
     *
     * @var DynamicLoader
     */
    protected DynamicLoader $loader;

    /**
     * Main application reference
     *
     * @var App
     */
    protected App $app;

    /**
     * BaseController constructor.
     */
    public function __construct(bool $registerInstance = false)
    {
        // standard references to main components
        $this->loader = DynamicLoader::getLoader();
        $this->config = $this->loader->getInstance(Config::class);
        $this->app = $this->loader->getInstance(App::class);

        if ($registerInstance) {
            // useful if a controller should not be present multiple times
            // in situations, where multiple instances are possible
            $this->loader->registerInstance(BaseController::class, $this);
        }
    }

    /**
     * Setup routine, setup information can be
     * passed directly from application before
     * main core ignition
     *
     * @param mixed ...$params
     * @return void
     */
    public function setup(...$params): void
    {
        // This method is executed everytime
        // a controller is instantiated by app
    }
}

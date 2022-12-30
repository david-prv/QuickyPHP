<?php

/*
 * Require the dynamic loader, which will
 * act as autoloader later-on
 */
require __DIR__ . "/core/DynamicLoader.php";

/*
 * Register the php-in-built autoloader function.
 * QuickyPHP does not use any namespaces or external
 * auto-loaders, like Composer or so.
 */
spl_autoload_register(function($className) {
    DynamicLoader::getLoader()->load($className);
});
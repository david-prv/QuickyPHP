<?php

require __DIR__ . "/core/DynamicLoader.php";

use app\core\DynamicLoader;

spl_autoload_register(function($class) {
    DynamicLoader::getLoader()->load($class);
});
<?php

require __DIR__ . "/core/DynamicLoader.php";

spl_autoload_register(function($class) {
    DynamicLoader::getLoader()->load($class);
});
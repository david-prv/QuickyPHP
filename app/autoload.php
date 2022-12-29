<?php

require __DIR__ . "/core/DynamicLoader.php";

spl_autoload_register(function($className) {
    DynamicLoader::getLoader()->load($className);
});
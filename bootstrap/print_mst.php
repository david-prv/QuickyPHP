<?php

use App\Core\DynamicLoader;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function () {
    DynamicLoader::getLoader()->getMethods()->dump();
});

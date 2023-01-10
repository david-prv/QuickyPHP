<?php

use App\Core\DynamicLoader;
use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    DynamicLoader::getLoader()->getMethods()->dump();

    return $response;
});

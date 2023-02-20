<?php

use Quicky\Core\DynamicLoader;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/", function (Request $_, Response $response) {
    DynamicLoader::getLoader()->getMethods()->dump();

    return $response;
});

return $app;

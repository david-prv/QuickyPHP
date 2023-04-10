<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::factory()
    ->withFunctionAlias("foo", "bar")
    ->enforceCatchError()
    ->build();

App::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Hello, World!");

    return $response;
});

App::route("GET", "/@name", function (Request $request, Response $response) {
    $response->write("Hello, %s!", $request->getArg("name"));

    return $response;
});

return $app;

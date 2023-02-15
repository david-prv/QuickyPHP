<?php

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

$app = App::create();

App::alias("get", function (string $pattern, callable $callback) {
    $router = App::router();
    $router->route("GET", $pattern, $callback);
});

App::get("/", function (Request $request, Response $response) {
    echo "<h1>Hello World</h1>";
    return $response;
});

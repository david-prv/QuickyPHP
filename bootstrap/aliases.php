<?php

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

$app = App::create();

App::alias("get", function (string $pattern, callable $callback, ...$middleware) {
    $router = App::router();
    $router->route("GET", $pattern, $callback, ...$middleware);
});

App::get("/", function (Request $_, Response $response) {
    $response->write("<h1>Hello World</h1>");
    return $response;
});

<?php

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

$app = App::create();

App::alias("A", App::class, false);

App::alias("get", function (string $pattern, callable $callback, ...$middleware) {
    $router = App::router();
    $router->route("GET", $pattern, $callback, ...$middleware);
});

App::alias("post", function (string $pattern, callable $callback, ...$middleware) {
    $router = App::router();
    $router->route("POST", $pattern, $callback, ...$middleware);
});

App::get("/", function (Request $_, Response $response) {
    $response->write("<h1>Hello World</h1>");
    return $response;
});

App::post("/", function (Request $_, Response $response) {
    $response->write("OK");
    return $response;
});

App::router()->dump();

App::alias("render", function (string $message) {
    echo $message;
});

App::render("* beep * This is a test... * boop *");

return $app;

<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Middleware\LoggingMiddleware;
use Quicky\Middleware\RateLimitMiddleware;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/", function (Request $_, Response $response) {
    $response->write("<h1>Test</h1>");
    $response->write("<p>Hallo Welt!</p>");
    return $response;
}, new RateLimitMiddleware(1, 5), new LoggingMiddleware());

return $app;

<?php

use App\Http\Request;
use App\Http\Response;
use App\Middleware\LoggingMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->write("<h1>Test</h1>");
    $response->write("<p>Hallo Welt!</p>");
    return $response;
}, new RateLimitMiddleware(1, 5), new LoggingMiddleware());

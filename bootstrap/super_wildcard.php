<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /start/[Some Path]/end/");

    return $response;
});

App::route("GET", "/start/*/end/", function (Request $_, Response $response) {
    $response->write("Standard Wildcard matched!");

    return $response;
});

App::route("GET", "/start/**/end", function (Request $_, Response $response) {
    $response->write("Super-Wildcard matched!");

    return $response;
});

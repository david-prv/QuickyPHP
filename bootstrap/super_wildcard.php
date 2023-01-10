<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /start/[Some Path]/end/");

    return $response;
});

Quicky::route("GET", "/start/*/end/", function (Request $_, Response $response) {
    $response->write("Standard Wildcard matched!");

    return $response;
});

Quicky::route("GET", "/start/**/end", function (Request $_, Response $response) {
    $response->write("Super-Wildcard matched!");

    return $response;
});

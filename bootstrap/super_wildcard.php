<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->send("Usage: /start/[Some Path]/end/");
});

Quicky::route("GET", "/start/*/end/", function (Request $_, Response $response) {
    $response->send("Standard Wildcard matched!");
});

Quicky::route("GET", "/start/**/end", function (Request $_, Response $response) {
    $response->send("Super-Wildcard matched!");
});

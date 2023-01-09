<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/start/*/end/", function (Request $_, Response $response) {
    $response->send("Standard Wildcard matched!");
});

Quicky::route("GET", "/start/**/end", function (Request $_, Response $response) {
    $response->send("Double Wildcard matched!");
});

<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /[SOME NUMBER]");

    return $response;
});

Quicky::route("GET", "/test/(\w+)/test/", function (Request $request, Response $response) {
    $response->write("<h1>Hallo</h1>");

    return $response;
});

Quicky::route("GET", "/{id:(\d+)}", function (Request $request, Response $response) {
    $response->write("Your number-ID was: %d", $request->getArg("id"));

    return $response;
});

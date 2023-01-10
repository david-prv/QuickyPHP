<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Hello, World!");

    return $response;
});

Quicky::route("GET", "/{name}", function (Request $request, Response $response) {
    $response->write("Hello, %s!", $request->getArg("name"));

    return $response;
});

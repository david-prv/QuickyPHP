<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $response->send("Hello, World!");
});

Quicky::route("GET", "/{name}", function (Request $request, Response $response) {
    $response->send("Hello, %s!", $request->getArg("name"));
});

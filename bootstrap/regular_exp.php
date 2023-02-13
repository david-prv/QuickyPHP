<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /[SOME NUMBER] OR /test/[ALPHANUM]/test " .
        "OR /user/[6 CHAR STRING]/profile/");

    return $response;
});

Quicky::route("GET", "/test/(\w+)/test/", function (Request $_, Response $response) {
    $response->write("Yup, that's correct.");

    return $response;
});

Quicky::route("GET", "/user/@username:([A-Z,a-z]*):6/profile/", function (Request $request, Response $response) {
    $response->write($request->getArg("username"));

    return $response;
});

Quicky::route("GET", "/@id:(\d+)", function (Request $request, Response $response) {
    $response->write("Your number-ID was: %d", $request->getArg("id"));

    return $response;
});

Quicky::route("GET", "/test2/@var::3-5", function (Request $request, Response $response) {
    $response->write($request->getArg("var"));

    return $response;
});

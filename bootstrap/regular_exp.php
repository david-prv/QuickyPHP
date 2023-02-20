<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /[SOME NUMBER] OR /test/[ALPHANUM]/test " .
        "OR /user/[6 CHAR STRING]/profile/");

    return $response;
});

App::route("GET", "/test/(\w+)/test/", function (Request $_, Response $response) {
    $response->write("Yup, that's correct.");

    return $response;
});

App::route("GET", "/user/@username:([A-Z,a-z]{6})/profile/", function (Request $request, Response $response) {
    $response->write($request->getArg("username"));

    return $response;
});

App::route("GET", "/@id:(\d+)", function (Request $request, Response $response) {
    $response->write("Your number-ID was: %d", $request->getArg("id"));

    return $response;
});

App::route("GET", "/test2/@var::3-5", function (Request $request, Response $response) {
    $response->write($request->getArg("var"));

    return $response;
});

return $app;

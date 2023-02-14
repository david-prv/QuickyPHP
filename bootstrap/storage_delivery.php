<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/storage/{filename}/**", function (Request $request, Response $response) {
    $response->sendFile($request->getArg("filename"));

    return $response;
});

App::route("GET", "/", function (Request $_, Response $response) {
    $response->write("Usage: /storage/[fileName]");

    return $response;
});

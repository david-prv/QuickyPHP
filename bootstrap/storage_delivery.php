<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/storage/{filename}/*", function (Request $request, Response $response) {
    $response->sendFile($request->getArg("filename"));
});

Quicky::route("GET", "/", function () {
    die("Usage: /storage/[fileName]");
});

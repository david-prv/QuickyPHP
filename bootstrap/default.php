<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $delay = number_format(microtime(true) - Quicky::session()->getCreatedAt(), 4);
    $response->render("index", array("P_TIME_S" => $delay));

    return $response;
});
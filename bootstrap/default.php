<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $delay = number_format(microtime(true) - Quicky::session()->getCreatedAt(), 5);
    $response->render("index", array(
        "P_TIME_S" => $delay
    ));

    return $response;
});

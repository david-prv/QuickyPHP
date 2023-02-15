<?php

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

$app = App::create();
App::session()->start();

App::route("GET", "/", function (Request $request, Response $response) {
    $delay = number_format(microtime(true) - App::session()->getCreatedAt(), 5);
    $response->render("index", array(
        "P_TIME_S" => $delay,
        "REQ_REF_ID" => $request->getID()
    ));

    return $response;
});

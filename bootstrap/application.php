<?php

use Quicky\App;
use Quicky\AppFactory;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Middleware\LoggingMiddleware;

$app = AppFactory::empty()
    ->state(S_PRODUCTION)
    ->middleware(LoggingMiddleware::class)
    ->build();

App::session()->start();

App::route("GET", "/", function (Request $request, Response $response) {
    $delta = number_format(
        microtime(true) - App::session()->getCreatedAt(), 5
    );

    $response->render("index", array(
        "P_TIME_S" => $delta,
        "REQ_REF_ID" => $request->getID()
    ));

    return $response;
});

return $app;

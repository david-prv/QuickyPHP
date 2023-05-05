<?php

use Quicky\App;
use Quicky\AppFactory;
use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\Middleware\LoggingMiddleware;

/* Factoring main application */
$app = AppFactory::empty()
    ->state(AppFactory::PRODUCTION)
    ->middleware(LoggingMiddleware::class)
    ->build();

/* App session starting */
App::session()->start();

/* Default route */
App::route("GET", "/", function (Request $request, Response $response) {
    // calculate the time which has passed in seconds
    $delta = number_format(microtime(true) - App::session()->getCreatedAt(), 5);

    // render the default view
    $response->render("index", array(
        "P_TIME_S" => $delta,
        "REQ_REF_ID" => $request->getID()
    ));

    // pass response object
    return $response;
});

/* Pass app to main thread */
return $app;

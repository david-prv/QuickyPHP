<?php
/*
|--------------------------------------------------------------------------
| Default Routes
|--------------------------------------------------------------------------
|
| The standard QuickyPHP installation has a default route
| pointing at "/", which is the default page when calling the IP
| address of the webserver.
|
*/

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

App::route("GET", "/", function (Request $request, Response $response) {
    $delta = number_format(
        microtime(true) - App::session()->getCreatedAt(),
        5
    );

    $response->render("index", array(
        "P_TIME_S" => $delta,
        "REQ_REF_ID" => $request->getID()
    ));

    return $response;
});

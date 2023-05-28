<?php
/*
|--------------------------------------------------------------------------
| Default Routes
|--------------------------------------------------------------------------
|
| The standard QuickyPHP installation has a default route
| pointing at "/", which is the default page when calling the IP
| address of the web-server.
|
*/

use Quicky\App;
use Quicky\Http\Request;
use Quicky\Http\Response;

App::route("GET", "/", function (Request $request, Response $response) {
    $response->render("index", array(
        "APP_VERSION" => App::config()->getProject("version"),
        "REQ_REF_ID" => $request->getID(),
        "SHOW_BANNER" => "hidden",
        "BANNER_PRIORITY" => ""
    ));

    return $response;
});

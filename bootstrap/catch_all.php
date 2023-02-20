<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

App::route("GET", "/**", function (Request $_, Response $response) {
    $response->write("Match!");

    return $response;
});

return $app;

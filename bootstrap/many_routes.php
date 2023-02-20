<?php

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();
App::session()->start();

$usedMethods = array("GET", "POST");
for ($i = 0; $i < 1000; $i++) {
    try {
        App::route(
            $usedMethods[random_int(0, 1)],
            "/" . (string)$i,
            function (Request $_, Response $response) use ($i) {
                $response->write("This is random route nr. %s", "$i");

                return $response;
            }
        );
    } catch (Exception $e) {
    }
}

App::route("GET", "/", function (Request $_, Response $response) {
    $router = App::router();
    $router->dump();

    return $response;
});

return $app;

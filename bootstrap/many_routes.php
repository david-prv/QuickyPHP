<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

$usedMethods = array("GET", "POST");
for ($i = 0; $i < 1000; $i++) {
    try {
        Quicky::route(
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

Quicky::route("GET", "/", function (Request $_, Response $response) {
    $router = Quicky::router();
    $router->dump();

    return $response;
});

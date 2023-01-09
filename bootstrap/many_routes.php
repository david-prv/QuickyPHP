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
                $response->send("This is random route nr. %s", "$i");
            }
        );
    } catch (Exception $e) {
    }
}

Quicky::route("GET", "/", function () {
    $router = Quicky::router();
    $router->dump();
});

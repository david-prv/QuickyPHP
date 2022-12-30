<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

App::get("/time", function(Request $request, Response $response) {
    $response->send("Request time: %s", $request->getTime());
});

App::get("/data/{var1}/{var2}", function(Request $request, Response $response) {
    var_dump($request->getArgs()["var2"]);
});

try {
    $app->run();
} catch (Exception $e) { die($e->getMessage()); }

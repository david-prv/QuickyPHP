<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::route("GET", "/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

App::route("GET", "/time", function(Request $request, Response $response) {
    $response->send("Request time: %s", $request->getTime());
});

try {
    $app->run();
} catch (Exception $e) { die($e->getMessage()); }

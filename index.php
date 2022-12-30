<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

App::get("/time", function(Request $request, Response $response) {
    $response->send("Request time: %s", $request->getTime());
});

try {
    $app->run();
} catch (Exception $e) { die($e->getMessage()); }

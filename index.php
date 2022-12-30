<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

App::get("/dump", function(Request $request, Response $response) {
    $response->send("Request dump:<br>%s", $request->toString());
});

App::get("/data/{var1}/{var2}", function(Request $request, Response $response) {
    var_dump($request->getArgs());
});

try {
    $app->run();
} catch (Exception $e) { die($e->getMessage()); }

<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function(Request $request, Response $response) {
    echo "Hello World";
});

App::get("/test/abc", function(Request $request, Response $response) {
    echo "Nice to meet you!";
});

try {
    $app->run();
} catch (Exception $e) {}

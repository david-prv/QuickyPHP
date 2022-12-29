<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function() {
    echo "Hello World";
});

App::get("/test/abc", function() {
    echo "Nice to meet you!";
});

$app->run();
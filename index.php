<?php

require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function (Request $request, Response $response) {
    $response->render("index", array("placeholder1" => "Hello", "placeholder2" => "World"));
});

App::get("/about", function (Request $request, Response $response) {
    $response->render("about");

});

App::get("/greet/{name}/{age}", function (Request $request, Response $response) {
    $response->send("Hello %s, you are %s years old.",
        $request->getArg("name"),
        $request->getArg("age"));
});

try {
    $app->run();
} catch (Exception $e) {
    die($e->getMessage());
}

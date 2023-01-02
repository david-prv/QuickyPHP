<?php

require __DIR__ . "/../../vendor/autoload.php";

$app = Quicky::create();

Quicky::session()->start();
Quicky::useMiddleware(new LoggingMiddleware());

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $response->render("index", array("placeholder1" => "Hello", "placeholder2" => "World"));
});

Quicky::route("GET","/greet/{name}/{age}", function (Request $request, Response $response) {
    $response->send("Hello %s, you are %s years old.",
        $request->getArg("name"),
        $request->getArg("age"));
});

Quicky::route("GET","/static/{name}", function (Request $request, Response $response) {
    $response->sendFile($request->getArg("name"));
});

Quicky::route("GET","/form", function (Request $request, Response $response) {
    $response->render("form", array("TOKEN" => Quicky::session()->generateCSRFToken()));
});

Quicky::route("GET","/form/post", function (Request $request, Response $response) {
    $response->send("hi, %s!", $request->getField("name"));
}, new CSRFMiddleware());


$app->run(true);

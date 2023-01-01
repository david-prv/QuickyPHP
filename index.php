<?php

require __DIR__ . "/vendor/autoload.php";

$app = Quicky::create();

Quicky::session()->start();
Quicky::useMiddleware(new LoggingMiddleware());

Quicky::get("/", function (Request $request, Response $response) {
    $response->render("index", array("placeholder1" => "Hello", "placeholder2" => "World"));
});

Quicky::get("/greet/{name}/{age}", function (Request $request, Response $response) {
    $response->send("Hello %s, you are %s years old.",
        $request->getArg("name"),
        $request->getArg("age"));
});

Quicky::get("/static/{name}", function(Request $request, Response $response) {
   $response->sendFile($request->getArg("name"));
});

Quicky::get("/form", function(Request $request, Response $response) {
    $response->render("form", array("TOKEN" => Quicky::session()->generateCSRFToken()));
});

Quicky::post("/form/post", function(Request $request, Response $response) {
    $response->send("hi, %s!", $request->getField("name"));
}, new CSRFMiddleware());

try {
    $app->run();
} catch (Exception $e) {}

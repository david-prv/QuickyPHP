<?php

require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create(Config::LOAD_FROM_JSON);

Quicky::session()->start();
Quicky::session()->set("test", "I am a test!");

Quicky::get("/", function (Request $request, Response $response) {
    $response->render("index", array("placeholder1" => "Hello", "placeholder2" => "World"));
});

Quicky::get("/about", function (Request $request, Response $response) {
    $response->render("about");
});

Quicky::get("/greet/{name}/{age}", function (Request $request, Response $response) {
    $response->send("Hello %s, you are %s years old.",
        $request->getArg("name"),
        $request->getArg("age"));
});

Quicky::get("/static/{name}", function(Request $request, Response $response) {
   $response->sendFile($request->getArg("name"));
});

Quicky::get("/profile/(\d+)", function(Request $request, Response $response) {
   $response->send("You looked for a profile page?");
});

Quicky::get("/wildcard/*/test", function(Request $request, Response $response) {
   $response->send("Wildcard Action, UwU");
});

Quicky::get("/session/{name}", function(Request $request, Response $response) {
   $response->send("Variable '%s' = '%s'",
       $request->getArg("name"),
       Quicky::session()->get($request->getArg("name")));
});

Quicky::post("/", function(Request $request, Response $response) {
   print_r($request->getData());
});

try {
    $app->run();
} catch (Exception $e) {
    die($e->getMessage());
}

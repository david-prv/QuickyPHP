<?php

require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();

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

try {
    $app->run();
} catch (Exception $e) {
    die($e->getMessage());
}

<?php

require __DIR__ . "/vendor/autoload.php";

$app = Quicky::create(Config::LOAD_DEFAULT);

Quicky::session()->start();
Quicky::session()->set("test", "I am a test!");
Quicky::session()->setRange(array("test2" => "we like testing", "test3" => "holymoly"));

Quicky::get("/middleware", function(Request $request, Response $response) {
    $response->send("<h1>Main Callback</h1>");
}, new ExampleMiddleware(), new ExampleMiddleware());

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

Quicky::get("/form", function(Request $request, Response $response) {
    $session = DynamicLoader::getLoader()->getInstance(Session::class);
    if ($session instanceof Session) {
        $response->render("form", array("TOKEN" => $session->generateCSRFToken()));
    }
});

Quicky::post("/form/post", function(Request $request, Response $response) {
    Quicky::session()->set("postName", $request->getField("name"));
    $response->send("hi, %s!", $request->getField("name"));
}, new CSRFMiddleware());

Quicky::post("/", function(Request $request, Response $response) {
   print_r($request->getData());
});

try {
    $app->run();
} catch (Exception $e) {
    die($e->getMessage());
}
$app->stop();

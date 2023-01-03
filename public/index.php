<?php
/*
|--------------------------------------------------------------------------
| Register Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides an automatically generated class loader for
| this application. Own implementations always had flaws.
|
*/

require __DIR__ . "/../vendor/autoload.php";

/*
|--------------------------------------------------------------------------
| Create QuickyPHP Application
|--------------------------------------------------------------------------
|
| Create an singleton instance of QuickyPHP. We will use
| Quicky with an active and secured session. Secure means, that the
| SessionID will be regenerating over-and-over again, to protect
| your application against basic Session-Hijacking.
|
*/

$app = Quicky::create();
Quicky::session()->start();

/*
|--------------------------------------------------------------------------
| Run Your Application
|--------------------------------------------------------------------------
|
| To make your application working fine, you need
| to register routes. Routes can contain RegEx patterns, named
| variables and wildcards. Here, we just add the index route for
| your application, such that https://your-domain.tld/ maps to your
| defined callback behaviour.
|
| The last line then ignites the Core of QuickyPHP, where requests
| are going to be routed and processed, middleware going to be
| executed and the application will work.
|
*/

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $delay = round(microtime(true) - Quicky::session()->getCreatedAt(), 5);
    $response->render("index", array("P_MS" => $delay));

    return $response;
});

$app->run(true);

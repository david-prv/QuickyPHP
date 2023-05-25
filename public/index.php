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

require_once __DIR__ . "/../vendor/autoload.php";

/*
|--------------------------------------------------------------------------
| Boot App from Bootstrap
|--------------------------------------------------------------------------
|
| A bootstrap is responsible for setting up the whole application:
| It creates a singleton instance of QuickyPHP and adds some routes.
| We will, by default, use Quicky with an active and secured session.
| Secure means, that the SessionID will be regenerating over-and-over again,
| to protect your application against basic Session-Hijacking.
|
*/

$app = require_once __DIR__ . "/../bootstrap/app.php";

/*
|--------------------------------------------------------------------------
| Run Your Application
|--------------------------------------------------------------------------
|
| This last line now ignites the Core of QuickyPHP, where requests
| are going to be routed and processed, middleware going to be
| executed and the application will work.
|
*/

$app->run();

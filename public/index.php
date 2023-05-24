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
| Pre-Boot preparations
|--------------------------------------------------------------------------
|
| The first step is to declare some standardized variable constants
| which can be used across the entirety of this application. It helps
| to implement some kind of convention and ensures consistency. Also,
| this way, we gain an easier usability for parameterized functions.
|
*/

require_once __DIR__ . "/../bootstrap/functions.php";

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

if (!verify_pre_condition($app)) {
    perform_boot_abort();
}
$app->run();
verify_post_condition($app);

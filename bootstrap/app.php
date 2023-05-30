<?php
/*
|--------------------------------------------------------------------------
| Application Construction
|--------------------------------------------------------------------------
|
| Here, the QuickyPHP application is being configured and
| constructed. This can be done via the AppFactory or manually
| using the "use()"-method. When everything is ready,
| "app" is being passed back to the main thread.
|
*/

use Quicky\App;
use Quicky\AppFactory;
use Quicky\Middleware\LoggingMiddleware;

$app = AppFactory::empty()
    ->state("production")
    ->middleware(LoggingMiddleware::class)
    ->build();

App::session()->start();

$app->register(\Quicky\Controllers\ExampleController::class);

require_once("./routes/web.php");

return $app;

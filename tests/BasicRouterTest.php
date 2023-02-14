<?php

declare(strict_types=1);

use Quicky\App;

require __DIR__ . "/QuickyTestCase.php";

class BasicRouterTest extends QuickyTestCase
{
    public function simpleRouteTest(): void
    {
        $app = App::create();
        $router = App::router();
        $router->route("GET", "/", function () {
        });

        $this->assertEquals(1, $router->countRoutes());

        $app->run();
    }
}

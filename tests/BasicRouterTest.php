<?php

declare(strict_types=1);

use App\Quicky;

require __DIR__ . "/QuickyTestCase.php";

class BasicRouterTest extends QuickyTestCase
{
    public function simpleRouteTest(): void
    {
        $app = Quicky::create();
        $router = Quicky::router();
        $router->route("GET", "/", function () {
        });

        $this->assertEquals(1, $router->countRoutes());

        $app->run();
    }
}

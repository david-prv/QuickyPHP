<?php

declare(strict_types=1);

use Quicky\App;
use Quicky\Utils\Exceptions\NotAResponseException;
use Quicky\Utils\Exceptions\UnknownRouteException;

require __DIR__ . "/QuickyTestCase.php";

final class BasicRouterTest extends QuickyTestCase
{
    /**
     * @throws NotAResponseException
     * @throws UnknownRouteException
     */
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

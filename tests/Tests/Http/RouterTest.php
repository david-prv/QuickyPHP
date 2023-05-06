<?php

namespace Quicky\Http;

use PHPUnit\Framework\TestCase;
use Quicky\App;
use Quicky\Utils\Exceptions\NotAResponseException;
use Quicky\Utils\Exceptions\UnknownRouteException;
use Tests\TestFactory;

class RouterTest extends TestCase
{

    public function testUseMiddleware()
    {

    }

    public function testCountRoutes()
    {

    }

    public function testRoute()
    {

    }

    public function test__invoke()
    {

    }

    public function testRouter()
    {

    }

    /**
     * @throws NotAResponseException
     * @throws UnknownRouteException
     */
    public function testGroup()
    {
        $app = TestFactory::getApp();

        $router = App::router();
        $this->assertTrue($router instanceof Router);

        $router->group(function () {
            return true;
        }, function () {
            App::route("GET", "/specific", function (Request $_, Response $response) {
                $response->write("OK!");
                return $response;
            });
        });

        $this->assertEquals(1, $router->countRoutes());

        TestFactory::generateRequest("GET", "/specific");
        $router(new Request(), new Response());

        $this->expectOutputString("OK!\r\n");
    }
}

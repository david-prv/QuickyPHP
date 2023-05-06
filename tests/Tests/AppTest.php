<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Quicky\App;
use Quicky\AppFactory;
use Quicky\Http\Request;
use Quicky\Http\Response;

class AppTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testRoute()
    {
        $app = TestFactory::getApp();

        App::route("GET", "/", function (Request $_, Response $response) {
            // silence is golden
            return $response;
        });

        $this->assertEquals(1, App::router()->countRoutes());

        for ($i = 0; $i < 20; $i++) {
            App::route(TestFactory::randomMethod(), "/" . TestFactory::randomString(),
                function (Request $_, Response $response) {
                // more silence is even better
                return $response;
            });
        }

        $this->assertEquals(21, App::router()->countRoutes());

        TestFactory::UNUSED($app);
    }

    public function testCreate()
    {
        $app = TestFactory::getApp();
        $this->assertNotNull($app);
    }

    public function testUse()
    {
        $app = TestFactory::getApp();
        App::use("env", AppFactory::PRODUCTION);

        $this->assertTrue(App::config()->isProd());

        App::use("alias", ["test", function () {
            echo "beep boop!";
        }]);

        App::test();

        $this->expectOutputString('beep boop!');
    }
}

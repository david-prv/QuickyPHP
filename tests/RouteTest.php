<?php declare(strict_types=1);

use Quicky\Http\Route;

require __DIR__ . "/QuickyTestCase.php";

final class RouteTest extends QuickyTestCase
{
    public function simpleAddRouteTest(): void
    {
        $route = new Route("GET", "/", function () {
        }, []);
        $this->assertInstanceOf(Route::class, $route);
        // $this->assertTrue($route->match("/", ));
    }
}

<?php declare(strict_types=1);

use App\Core\Config;
use App\Core\Managers\SessionManager;
use App\Core\View;
use App\Http\Router;
use App\Quicky;
use App\Utils\Exceptions\UnknownCallException;

require __DIR__ . "/QuickyTestCase.php";

class DispatcherTest extends QuickyTestCase
{
    public function testSessionDispatch(): void
    {
        $instance = Quicky::create();
        $this->assertInstanceOf(
            SessionManager::class,
            $instance::session()
        );
    }

    public function testViewDispatch(): void
    {
        $instance = Quicky::create();
        $this->assertInstanceOf(
            View::class,
            $instance::view()
        );
    }

    public function testRouterDispatch(): void
    {
        $instance = Quicky::create();
        $this->assertInstanceOf(
            Router::class,
            $instance::router()
        );
    }

    public function testConfigDispatch(): void
    {
        $instance = Quicky::create();
        $this->assertInstanceOf(
            Config::class,
            $instance::config()
        );
    }

    public function testIllegalMethodDispatch(): void
    {
        $instance = Quicky::create();
        try {
            $instance::findRouteByHash("123abc");
            $this->fail("Method shouldn't be dispatching");
        } catch (Exception $e) {
            $this->assertInstanceOf(UnknownCallException::class, $e);
        }
    }

    public function testUnknownMethodDispatch(): void
    {
        $instance = Quicky::create();
        try {
            $instance::unknownMethod();
            $this->fail("Unknown method called, should throw an exception!");
        } catch (UnknownCallException $e) {
            $this->assertInstanceOf(
                UnknownCallException::class,
                $e
            );
        }
    }
}
<?php declare(strict_types=1);

use Quicky\Core\Config;
use Quicky\Core\Managers\SessionManager;
use Quicky\Core\View;
use Quicky\Http\Router;
use Quicky\App;
use Quicky\Utils\Exceptions\UnknownCallException;

require __DIR__ . "/QuickyTestCase.php";

class DispatcherTest extends QuickyTestCase
{
    public function testSessionDispatch(): void
    {
        $instance = App::create();
        $this->assertInstanceOf(
            SessionManager::class,
            $instance::session()
        );
    }

    public function testViewDispatch(): void
    {
        $instance = App::create();
        $this->assertInstanceOf(
            View::class,
            $instance::view()
        );
    }

    public function testRouterDispatch(): void
    {
        $instance = App::create();
        $this->assertInstanceOf(
            Router::class,
            $instance::router()
        );
    }

    public function testConfigDispatch(): void
    {
        $instance = App::create();
        $this->assertInstanceOf(
            Config::class,
            $instance::config()
        );
    }

    public function testIllegalMethodDispatch(): void
    {
        $instance = App::create();
        try {
            $instance::findRouteByHash("123abc");
            $this->fail("Method shouldn't be dispatching");
        } catch (Exception $e) {
            $this->assertInstanceOf(UnknownCallException::class, $e);
        }
    }

    public function testUnknownMethodDispatch(): void
    {
        $instance = App::create();
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
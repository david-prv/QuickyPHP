<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

class DispatcherTest extends QuickyTestCase
{
    public function testSessionDispatch(): void
    {
        $instance = Quicky::create();
        $this->assertInstanceOf(
            Session::class,
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
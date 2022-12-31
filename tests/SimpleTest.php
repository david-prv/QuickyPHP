<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

final class SimpleTest extends QuickyTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Quicky::class,
            Quicky::create()
        );
    }

    public function testCanBeStarted(): void
    {
        $instance = Quicky::create();
        $this->assertNotNull($instance);
        $this->assertInstanceOf(Quicky::class, $instance);

        try {
            $instance->run();
            $this->fail("No route was defined, Quicky should throw an exception!");
        } catch (UnknownRouteException $e) {
        }
    }
}
<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

use Quicky\App;

final class SimpleTest extends QuickyTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            App::class,
            App::create()
        );
    }

    public function testCanBeStarted(): void
    {
        $instance = App::create();
        $this->assertNotNull($instance);
        $this->assertInstanceOf(App::class, $instance);
    }
}
<?php declare(strict_types=1);

use App\Core\Config;
use App\Core\DynamicLoader;

require __DIR__ . "/QuickyTestCase.php";

class DynamicLoaderTest extends QuickyTestCase
{
    public function testSimpleInstance(): void
    {
        $instance = DynamicLoader::getLoader()->getInstance(Config::class);
        $this->assertInstanceOf(
            Config::class,
            $instance
        );
    }

    public function testDoubleInstanceLoading(): void
    {
        $loader = DynamicLoader::getLoader();
        $instance1 = $loader->getInstance(Config::class);

        $this->assertInstanceOf(
            Config::class,
            $instance1
        );

        $instance2 = $loader->getInstance(Config::class);

        $this->assertInstanceOf(
            Config::class,
            $instance2
        );

        $this->assertSame($instance1, $instance2);
    }

    public function testSelfInstance(): void
    {
        $loader = DynamicLoader::getLoader();
        $instanceLoader = $loader->getInstance(DynamicLoader::class);

        $this->assertInstanceOf(
            DynamicLoader::class,
            $instanceLoader
        );

        $this->assertSame($loader, $instanceLoader);
    }

    public function testFindMethodView(): void
    {
        $className = DynamicLoader::getLoader()->findMethod("view");

        $this->assertEquals("View", $className);
    }

    public function testFindIllegalMethod(): void
    {
        $className = DynamicLoader::getLoader()->findMethod("execute");
        $this->assertNull($className);
    }

    public function testFindUnknownMethod(): void
    {
        $className = DynamicLoader::getLoader()->findMethod("thisMethodDoesNotExist123");
        $this->assertNull($className);
    }
}
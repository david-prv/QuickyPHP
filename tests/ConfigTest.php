<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

class ConfigTest extends QuickyTestCase
{
    const CURRENT_NAME = "Quicky - PHP Framework";
    const CURRENT_VERSION = "0.0.1";
    const CURRENT_ENV = "development";
    const CURRENT_AUTHOR = "David Dewes";

    public function testLoadByJSON(): void
    {
        Quicky::create(Config::LOAD_FROM_JSON);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);

        $this->assertTrue($config instanceof Config);
        $this->check($config);
    }

    public function testLoadByDefault(): void
    {
        Quicky::create(Config::LOAD_DEFAULT);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);

        $this->assertTrue($config instanceof Config);
        $this->check($config);
    }

    private function check(object $config)
    {
        if ($config instanceof Config) {

            $this->assertEquals($this::CURRENT_NAME, $config->getName());
            $this->assertEquals($this::CURRENT_AUTHOR, $config->getAuthor());
            $this->assertEquals($this::CURRENT_ENV, $config->getEnv());
            $this->assertEquals($this::CURRENT_VERSION, $config->getVersion());
            $this->assertEquals(3600, $config->getCacheExpiration());
            $this->assertEquals("/quicky/storage", $config->getStoragePath());
            $this->assertEquals("/quicky/views", $config->getViewsPath());
            $this->assertTrue($config->isCacheActive());
            $this->assertFalse($config->isProd());
            $this->assertTrue($config->isDev());

        } else { $this->fail(); }
    }
}
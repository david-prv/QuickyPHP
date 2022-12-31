<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

class ConfigTest extends QuickyTestCase
{
    public function testLoadByJSON(): void
    {

    }

    public function testLoadByEnv(): void
    {

    }

    public function testLoadByDefault(): void
    {
        Quicky::create(Config::LOAD_DEFAULT);
        $config = DynamicLoader::getLoader()->getInstance(Config::class);

        $this->assertTrue($config instanceof Config);

        if ($config instanceof Config) {

            $this->assertEquals("Quicky - PHP Framework", $config->getName());
            $this->assertEquals("David Dewes", $config->getAuthor());
            $this->assertEquals("development", $config->getEnv());
            $this->assertTrue($config->isCacheActive());
            $this->assertFalse($config->isProd());
            $this->assertTrue($config->isDev());

        } else { $this->fail(); }
    }
}
<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{

    public function testRoute()
    {

    }

    public function testCreate()
    {
        /** @var mixed $app */

        $app = TestFactory::getApp();
        $this->assertNotNull($app);
    }

    public function testRender()
    {
    }

    public function testUse()
    {

    }
}

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
        $app = TestFactory::getApp();
        $this->assertNotNull("awd");
    }

    public function testRender()
    {
    }

    public function testUse()
    {

    }
}

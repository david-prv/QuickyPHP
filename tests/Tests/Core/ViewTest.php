<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use Quicky\App;
use Quicky\Core\View;
use Quicky\Utils\Exceptions\ViewNotFoundException;
use Tests\TestFactory;

class ViewTest extends TestCase
{
    /**
     * @throws ViewNotFoundException
     */
    public function testRender()
    {
        $app = TestFactory::getApp();
        $view = App::view();

        $this->assertTrue($view instanceof View);

        $this->expectException(ViewNotFoundException::class);
        $view::render("I_am_not_a_view");
    }
}

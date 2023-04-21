<?php

namespace Tests;

use Quicky\App;

class TestFactory
{

    private static ?App $instance = null;

    public static function getApp(): App
    {
        if (self::$instance === null) {
            self::$instance = App::create();
        }
        return self::$instance;
    }

}
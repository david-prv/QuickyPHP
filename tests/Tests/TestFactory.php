<?php

namespace Tests;

use Quicky\App;

class TestFactory
{

    private static ?App $instance = null;

    public static function generateRequest(string $method = "GET", string $path = "/"): void
    {
        $_SERVER = [
            "REQUEST_METHOD" => strtoupper($method),
            "REQUEST_URI" => $path,
            "REQUEST_TIME" => time(),
            "REMOTE_ADDR" => "127.0.0.1",
            "HTTPS" => "true",
            "HTTP_REFERER" => "",
        ];
    }

    public static function getApp(): App
    {
        self::generateRequest();
        chdir("../");

        if (self::$instance === null) {
            self::$instance = App::create();
        }
        return self::$instance;
    }

}
<?php

namespace Tests;

use Exception;
use Quicky\App;

class TestFactory
{

    private static ?App $instance = null;

    /**
     * @param $x
     * @return void
     */
    public static function UNUSED($x): void
    {
        // $x is intentionally unused...
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function randomMethod(): string
    {
        $methods = ["GET", "POST", "PUT", "DELETE", "PATCH", "UPDATE"];
        return $methods[random_int(0, count($methods)-1)];
    }

    /**
     * @param int $length
     * @return string
     */
    public static function randomString(int $length = 5): string
    {
        // taken from: https://stackoverflow.com/questions/4356289/php-random-string-generator
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @param string $method
     * @param string $path
     * @return void
     */
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

    /**
     * @return App
     */
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
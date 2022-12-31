<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class QuickyTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        chdir(getcwd() . "/../");

        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_TIME"] = time();
        $_SERVER["REMOTE_ADDR"] = "127.0.0.1";
        $_SERVER["REMOTE_HOST"] = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $_SERVER["REMOTE_PORT"] = "80";
    }
}
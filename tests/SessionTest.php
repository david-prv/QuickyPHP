<?php declare(strict_types=1);

require __DIR__ . "/QuickyTestCase.php";

class SessionTest extends QuickyTestCase
{
    public function testSessionStart(): void
    {
        $app = Quicky::create();
        $session = Quicky::session();

        if ($session instanceof Session) {
            $this->assertNull($session->getId());
            $session->start();

            try {
                $app->run();
            } catch (UnknownRouteException $e) {
                $this->fail($e->getMessage());
            }

            $this->assertNotNull($session->getId());
            $this->assertNotNull($session->getCreatedAt());
        } else $this->fail();
    }

    public function testSessionDestroy(): void
    {

    }

    public function testSessionVariables(): void
    {

    }

    public function testSetRange(): void
    {

    }

    public function testOverridePredefined(): void
    {

    }
}
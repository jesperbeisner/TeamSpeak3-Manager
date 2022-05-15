<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Server;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Server
 */
class UserTest extends TestCase
{
    private Server $server;

    protected function setUp(): void
    {
        $this->server = new Server();
    }

    /**
     * @covers \App\Entity\Server::setToken
     */
    public function test_set_token(): void
    {
        $this->server->setToken('1234567890');

        $this->assertSame('1234567890', $this->server->getToken());
    }
}

<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Server;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends AbstractFixtures
{
    public function load(ObjectManager $manager): void
    {
        $server = new Server();
        $server->setPort(9987);
        $server->setWebQueryPort(10080);
        $server->setContainerName('TeamSpeak3-Server-9987');
        $server->setSynchronized(false);
        $manager->persist($server);

        $server = new Server();
        $server->setPort(20000);
        $server->setWebQueryPort(20080);
        $server->setContainerName('TeamSpeak3-Server-20000');
        $manager->persist($server);

        $server = new Server();
        $server->setPort(25000);
        $server->setWebQueryPort(25080);
        $server->setContainerName('TeamSpeak3-Server-25000');
        $manager->persist($server);

        $server = new Server();
        $server->setPort(30000);
        $server->setWebQueryPort(30080);
        $server->setContainerName('TeamSpeak3-Server-30000');
        $manager->persist($server);

        $manager->flush();
    }
}

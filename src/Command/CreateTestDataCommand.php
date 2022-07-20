<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Server;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-test-data',
    description: 'Fills the database with test data.'
)]
class CreateTestDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ([20000, 25000, 30000] as $port) {
            $server = new Server();
            $server->setPort($port);
            $server->setWebQueryPort($port + 80);
            $server->setContainerName('TeamSpeak3-Server-' . $port);

            $this->entityManager->persist($server);
        }

        $this->entityManager->flush();

        $io->success('Test data created successfully.');

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Server;
use App\Logger\DiscordLogger;
use App\Repository\ServerRepository;
use App\Service\DockerRunService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveServersCommand extends Command
{
    protected static $defaultName = 'app:remove-servers';
    protected static $defaultDescription = 'Removes all started teamspeak servers older than 24 hours.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DockerRunService $dockerRunService,
        private readonly DiscordLogger $discordLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ServerRepository $serverRepository */
        $serverRepository = $this->entityManager->getRepository(Server::class);
        $servers = $serverRepository->findAllStartedAndSynchronizedServersOlderThan24Hours();

        $logText = '';

        foreach ($servers as $server) {
            $this->dockerRunService->stopTeamSpeakServer($server);

            $server->reset();
            $this->entityManager->flush();

            $logText .= "Server with port {$server->getPort()} was stopped by 'app:remove-teamspeak-server' command" . PHP_EOL;
        }

        if (strlen($logText) > 0) {
            $logText = trim($logText);
            $this->discordLogger->info($logText);
        }

        $output->writeln(count($servers) . ' servers found and successfully stopped.');

        return Command::SUCCESS;
    }
}

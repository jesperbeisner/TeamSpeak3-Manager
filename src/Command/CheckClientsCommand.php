<?php

declare(strict_types=1);

namespace App\Command;

use App\Data\Client;
use App\Entity\Live;
use App\Entity\NameHistory;
use App\Entity\OnlineHistory;
use App\Entity\Server;
use App\Repository\LiveRepository;
use App\Repository\NameHistoryRepository;
use App\Repository\OnlineHistoryRepository;
use App\Repository\ServerRepository;
use App\Service\TeamSpeakService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckClientsCommand extends Command
{
    protected static $defaultName = 'app:check-clients';
    protected static $defaultDescription = 'Checks if someone has gone online or offline.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamSpeakService $teamSpeakService,
        private readonly LoggerInterface $discordLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(75);
        $output->writeln("Command 'app:check-clients' started.");

        /** @var ServerRepository $serverRepository */
        $serverRepository = $this->entityManager->getRepository(Server::class);
        $servers = $serverRepository->findAllStartedServers();

        if (count($servers) > 0) {
            /** @var LiveRepository $liveRepository */
            $liveRepository = $this->entityManager->getRepository(Live::class);

            for ($i = 0; $i < 5; $i++) {
                foreach ($servers as $server) {
                    /** @var Live[] $newOfflineClients */
                    $newOfflineClients = [];

                    /** @var Client[] $newOnlineClients */
                    $newOnlineClients = [];

                    // Get all 'live' database clients from last check
                    $databaseClients = $liveRepository->findBy(['server' => $server]);

                    // Get all actual live clients from the server directly
                    $teamSpeakClients = $this->teamSpeakService->getClientList($server);

                    // Check if we have some new names we have not seen before
                    $this->checkForNewNames($server, $teamSpeakClients);

                    $teamSpeakClients = $this->filterServeradminAndMultipleConnections($teamSpeakClients);

                    // Check if all clients from the last check are still online
                    foreach ($databaseClients as $databaseClient) {
                        $found = false;
                        foreach ($teamSpeakClients as $teamSpeakClient) {
                            if ($databaseClient->getUuid() === $teamSpeakClient->uuid) {
                                $found = true;
                                break;
                            }
                        }

                        // Was not found in the actual online clients, must have gone offline since the last check
                        if ($found === false) {
                            $newOfflineClients[] = $databaseClient;
                        }
                    }

                    // Check if all clients from live check were already online
                    foreach ($teamSpeakClients as $teamSpeakClient) {
                        $found = false;
                        foreach ($databaseClients as $databaseClient) {
                            if ($teamSpeakClient->uuid === $databaseClient->getUuid()) {
                                $found = true;
                                break;
                            }
                        }

                        // Was not found in the list from the last check, must have gone online since the last check
                        if ($found === false) {
                            $newOnlineClients[] = $teamSpeakClient;
                        }
                    }

                    // Update the database with the actual live clients
                    $this->updateLiveDatabaseClients($server, $teamSpeakClients);

                    // Create a new OnlineHistory for each client who newly came online
                    foreach ($newOnlineClients as $newOnlineClient) {
                        $onlineHistory = new OnlineHistory();
                        $onlineHistory->setServer($server);
                        $onlineHistory->setUsername($newOnlineClient->nickname);
                        $onlineHistory->setUuid($newOnlineClient->uuid);

                        $this->entityManager->persist($onlineHistory);
                    }

                    /** @var OnlineHistoryRepository $onlineHistoryRepository */
                    $onlineHistoryRepository = $this->entityManager->getRepository(OnlineHistory::class);

                    // Search for the started OnlineHistory and close it
                    foreach ($newOfflineClients as $newOfflineClient) {
                        $onlineHistory = $onlineHistoryRepository->findOneBy([
                            'server' => $server,
                            'uuid' => $newOfflineClient->getUuid(),
                        ]);

                        $onlineHistory?->setEndSession(new DateTime());
                    }

                    $this->entityManager->flush();

                    $this->logChanges($server, $newOnlineClients, $newOfflineClients);
                }

                $output->writeln("Check number " . ($i + 1));

                sleep(10);
            }
        } else {
            $output->writeln('No online servers found');
        }

        $output->writeln("Command 'app:check-clients' finished.");

        return Command::SUCCESS;
    }

    /**
     * @param Client[] $clients
     * @return Client[]
     */
    private function filterServeradminAndMultipleConnections(array $clients): array
    {
        $seenUuids = [];

        foreach ($clients as $key => $client) {
            if ($client->uuid === 'serveradmin') {
                unset($clients[$key]);
            }

            if (in_array($client->uuid, $seenUuids)) {
                unset($clients[$key]);
            }

            $seenUuids[] = $client->uuid;
        }

        return $clients;
    }

    /**
     * @param Client[] $teamSpeakClients
     */
    private function updateLiveDatabaseClients(Server $server, array $teamSpeakClients): void
    {
        // Step 1: Remove all
        $this->entityManager->getRepository(Live::class)->removeAllByServer($server);

        // Step 2: Create new current live list
        foreach ($teamSpeakClients as $teamSpeakClient) {
            $live = new Live();
            $live->setServer($server);
            $live->setUsername($teamSpeakClient->nickname);
            $live->setUuid($teamSpeakClient->uuid);

            $this->entityManager->persist($live);
        }

        $this->entityManager->flush();
    }

    /**
     * @param Client[] $teamSpeakClients
     */
    private function checkForNewNames(Server $server, array $teamSpeakClients): void
    {
        /** @var NameHistoryRepository $nameHistoryRepository */
        $nameHistoryRepository = $this->entityManager->getRepository(NameHistory::class);

        foreach ($teamSpeakClients as $teamSpeakClient) {
            if ($teamSpeakClient->uuid === 'serveradmin') {
                continue;
            }

            $nameHistory = $nameHistoryRepository->findOneBy([
                'server' => $server,
                'username' => $teamSpeakClient->nickname,
                'uuid' => $teamSpeakClient->uuid,
            ]);

            // Not found means not seen username for this uuid before
            if ($nameHistory === null) {
                $nameHistory = new NameHistory();
                $nameHistory->setServer($server);
                $nameHistory->setUsername($teamSpeakClient->nickname);
                $nameHistory->setUuid($teamSpeakClient->uuid);

                $this->entityManager->persist($nameHistory);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param Client[] $newOnlineClients
     * @param Live[] $newOfflineClients
     */
    private function logChanges(Server $server, array $newOnlineClients, array $newOfflineClients): void
    {
        $logText = '';

        if (count($newOnlineClients) > 0) {
            $logText .= "**New online clients for server with port {$server->getPort()}:**" . PHP_EOL;

            foreach ($newOnlineClients as $newOnlineClient) {
                $logText .= "Username: {$newOnlineClient->nickname} - Uuid: {$newOnlineClient->uuid}" . PHP_EOL;
            }
        }

        if (count($newOnlineClients) > 0 && count($newOfflineClients) > 0) {
            $logText .= PHP_EOL;
        }

        if (count($newOfflineClients) > 0) {
            $logText .= "**New offline clients for server with port {$server->getPort()}:**" . PHP_EOL;

            foreach ($newOfflineClients as $newOfflineClient) {
                $logText .= "Username: {$newOfflineClient->getUsername()} - Uuid: {$newOfflineClient->getUuid()}" . PHP_EOL;
            }
        }

        $logText = trim($logText);

        if (strlen($logText) > 0) {
            $this->discordLogger->info($logText);
        }
    }
}

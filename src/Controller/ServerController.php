<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\KeyHistory;
use App\Entity\Server;
use App\Entity\ServerHistory;
use App\Exception\ApiKeyOrTokenNotFoundException;
use App\Exception\TeamSpeakWebQueryException;
use App\Repository\ServerHistoryRepository;
use App\Repository\ServerRepository;
use App\Service\DockerLogsService;
use App\Service\DockerRunService;
use App\Service\TeamSpeakChannelService;
use App\Service\TeamSpeakService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $discordLogger,
        private readonly EntityManagerInterface $entityManager,
        private readonly DockerRunService $dockerRunService,
        private readonly DockerLogsService $dockerLogsService,
        private readonly TeamSpeakService $teamSpeakService,
        private readonly TeamSpeakChannelService $teamSpeakChannelService,
    ) {}

    #[Route('/server/{port<\d+>}/start', name: 'server-start', methods: ['POST', 'GET'])]
    public function serverStart(int $port): JsonResponse
    {
        $server = $this->getServer($port);

        if ($server->getStarted() !== null) {
            return $this->errorResponse('Server is already started. Refresh the page to see possible changes.');
        }

        /** @var ServerHistoryRepository $serverHistoryRepository */
        $serverHistoryRepository = $this->entityManager->getRepository(ServerHistory::class);
        if (null !== $serverHistory = $serverHistoryRepository->findLastServerStartedHistory($server)) {
            $time = (new DateTime())->getTimestamp() - $serverHistory->getCreated()->getTimestamp();
            if ($time < 60) {
                $available = 60 - $time;
                return $this->errorResponse("The last start was less than a minute ago. Try again in $available seconds.");
            }
        }

        try {
            $this->dockerRunService->startTeamSpeakServer($server);
        } catch (ProcessFailedException $e) {
            $this->discordLogger->error($e->getMessage());

            return $this->errorResponse('An error has occurred. Please try again or contact Jesper if the error persists.');
        }

        try {
            [$apiKey, $token] = $this->dockerLogsService->getApiKeyAndToken($server);
        } catch (ProcessFailedException|ApiKeyOrTokenNotFoundException $e) {
            $this->discordLogger->error($e->getMessage());
            $this->dockerRunService->stopTeamSpeakServer($server);

            return $this->errorResponse('An error has occurred. Please try again or contact Jesper if the error persists.');
        }

        $started = new DateTime();

        $server->setApiKey($apiKey);
        $server->setToken($token);
        $server->setStarted($started);

        $keyHistory = new KeyHistory();
        $keyHistory->setServer($server);
        $keyHistory->setApiKey($apiKey);
        $keyHistory->setToken($token);
        $keyHistory->setCreated($started);

        $this->entityManager->persist($keyHistory);

        try {
            $this->teamSpeakService->setServerName($server, 'teamspeak.jesperbeisner.dev');
            $this->teamSpeakService->setDefaultChannelName($server, "[cspacer1337]🚀 W E L C O M E 🚀");
            $this->teamSpeakChannelService->createChannels($server);
        } catch (TransportException|TeamSpeakWebQueryException $e) {
            $this->discordLogger->error($e->getMessage());
            $this->dockerRunService->stopTeamSpeakServer($server);

            return $this->errorResponse('An error has occurred. Please try again or contact Jesper if the error persists.');
        }

        $serverHistory = new ServerHistory();
        $serverHistory->setServer($server);
        $serverHistory->setAction('started');

        $this->entityManager->persist($serverHistory);
        $this->entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Server was successfully started.',
            'data' => [
                'port' => $server->getPort(),
                'token' => $server->getToken(),
                'date' => $started->format('d.m.Y H:i:s'),
            ],
        ]);
    }

    #[Route('/server/{port<\d+>}/stop', name: 'server-stop', methods: ['POST'])]
    public function serverStop(int $port): JsonResponse
    {
        $server = $this->getServer($port);

        if ($server->getStarted() === null) {
            return $this->json([
                'status' => 'success',
                'message' => "Server with port {$server->getPort()} was successfully stopped",
            ]);
        }

        try {
            $this->dockerRunService->stopTeamSpeakServer($server);
        } catch (ProcessFailedException $e) {
            $this->discordLogger->error($e->getMessage());

            return $this->errorResponse('An error has occurred. Please try again or contact Jesper if the error persists.');
        }

        $server->reset();

        $serverHistory = new ServerHistory();
        $serverHistory->setServer($server);
        $serverHistory->setAction('stopped');

        $this->entityManager->persist($serverHistory);
        $this->entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => "Server with port {$server->getPort()} was successfully stopped",
        ]);
    }

    private function getServer(int $port): Server
    {
        /** @var ServerRepository $serverRepository */
        $serverRepository = $this->entityManager->getRepository(Server::class);

        if (null === $server = $serverRepository->findOneBy(['port' => $port, 'synchronized' => true])) {
            throw $this->createNotFoundException();
        }

        return $server;
    }

    private function errorResponse(string $message): JsonResponse
    {
        return $this->json(['status' => 'failure', 'message' => $message]);
    }
}

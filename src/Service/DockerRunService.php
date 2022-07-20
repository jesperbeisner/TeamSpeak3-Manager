<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Server;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerRunService
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function startTeamSpeakServer(Server $server): void
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $queryIpAllowlistFileName = "$projectDir/docker/teamspeak/query_ip_allowlist.txt";

        $process = new Process([
            "docker", "run", "-d",
            "--name", $server->getContainerName(),
            "-p", "{$server->getPort()}:9987/udp",
            "-p", "{$server->getWebQueryPort()}:10080",
            "--restart", "always",
            "-v", "$queryIpAllowlistFileName:/var/ts3server/query_ip_allowlist.txt",
            "-e", "TS3SERVER_LICENSE=accept",
            "-e", "TS3SERVER_QUERY_PROTOCOLS=http",
            "teamspeak:3.13.6",
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Give the teamspeak server container some time to set up correctly
        sleep(3);
    }

    public function stopTeamSpeakServer(Server $server): void
    {
        $process = new Process(['docker', 'rm', '-f', $server->getContainerName()]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}

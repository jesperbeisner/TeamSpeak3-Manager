<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Server;
use App\Exception\ApiKeyOrTokenNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerLogsService
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {}

    /**
     * @return array<string>
     */
    public function getApiKeyAndToken(Server $server): array
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $credentialsFileName = "$projectDir/var/{$server->getContainerName()}.txt";

        $process = Process::fromShellCommandline(
            "docker logs {$server->getContainerName()} 2> $credentialsFileName"
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        /** @var string $content */
        $content = file_get_contents($credentialsFileName);

        unlink($credentialsFileName);

        $apiKey = null;
        $token = null;

        foreach (explode("\n", $content) as $textLine) {
            if (str_contains($textLine, 'apikey')) {
                $apiKey = trim($textLine);
                $apiKey = str_replace('apikey= "', '', $apiKey);
                $apiKey = str_replace('"', '', $apiKey);
            }

            if (str_contains($textLine, 'token')) {
                $token = trim($textLine);
                $token = str_replace('token=', '', $token);
            }
        }

        if (null === $apiKey || null === $token) {
            throw new ApiKeyOrTokenNotFoundException('ApiKey or Token not found, something went wrong!');
        }

        return [$apiKey, $token];
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Server;
use App\Exception\TeamSpeakWebQueryException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractTeamSpeakService
{
    public function __construct(
        protected readonly HttpClientInterface $httpClient,
    ) {
    }

    protected function makeRequest(Server $server, string $url): ResponseInterface
    {
        return $this->httpClient->request('GET', "http://127.0.0.1:{$server->getWebQueryPort()}" . $url, [
            'headers' => [
                'x-api-key' => $server->getApiKey(),
            ],
        ]);
    }

    /**
     * @return array<int, array<string, string>>|null
     */
    protected function checkResponseForError(ResponseInterface $response): ?array
    {
        $data = null;

        $result = $response->toArray(false);

        // Better safe than sorry
        usleep(100000);

        if (!isset($result['status']['code'])) {
            throw new TeamSpeakWebQueryException(
                'Something went wrong. URL which caused the exception: ' . $response->getInfo()['url']
            );
        }

        if (0 !== $result['status']['code']) {
            throw new TeamSpeakWebQueryException($result['status']['message']);
        }

        if (array_key_exists('body', $result)) {
            /** @var array<int, array<string, string>> $data */
            $data = $result['body'];
        }

        return $data;
    }
}

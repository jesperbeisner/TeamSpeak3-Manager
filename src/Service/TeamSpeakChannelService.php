<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Server;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TeamSpeakChannelService extends AbstractTeamSpeakService
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        HttpClientInterface $httpClient,
    ) {
        parent::__construct($httpClient);
    }

    public function createChannels(Server $server): void
    {
        //$this->makeRequest($server, "/1/servergroupaddperm?sgid=2&permsid=b_client_ignore_antiflood&permvalue=1&permnegated=0&permskip=1");

        $channelLayout = $this->getChannelLayout();

        $spacerId = 0;
        foreach ($channelLayout as $channel) {
            /** @var string $channelName */
            $channelName = $channel['name'];

            if ($channel['spacer'] === true) {
                $channelName = "[*spacer$spacerId]" . $channelName;
                $spacerId++;
            }

            if ($channel['center'] === true) {
                $channelName = "[cspacer$spacerId]" . $channelName;
                $spacerId++;
            }

            $channelId = $this->createChannel($server, $channelName, $channel['max_clients']);

            if ($channel['talk_power'] > 0) {
                $this->setTalkPower($server, $channelId, $channel['talk_power']);
            }
        }
    }

    private function createChannel(Server $server, string $channelName, int $maxClients = -1): int
    {
        $url = "/1/channelcreate?channel_flag_permanent=1&channel_name=$channelName";

        if (-1 === $maxClients) {
            $url .= "&channel_maxclients=-1&channel_flag_maxclients_unlimited=1";
        } else {
            $url .= "&channel_maxclients=$maxClients&channel_flag_maxclients_unlimited=0";
        }

        $response = $this->makeRequest($server, $url);

        $this->checkResponseForError($response);

        return (int) $response->toArray(false)['body'][0]['cid'];
    }

    private function setTalkPower(Server $server, int $channelId, int $talkPower): void
    {
        $url = "/1/channeladdperm?cid=$channelId&permsid=i_client_needed_talk_power&permvalue=$talkPower";

        $response = $this->makeRequest($server, $url);

        $this->checkResponseForError($response);
    }

    /**
     * @return array<string, mixed>
     */
    private function getChannelLayout(): array
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        /** @var array<string, mixed> $channelLayout */
        $channelLayout = require $projectDir . '/server-channel-layout.php';

        return $channelLayout;
    }
}

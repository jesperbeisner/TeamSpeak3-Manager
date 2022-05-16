<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\Client;
use App\Entity\Server;
use App\Exception\TeamSpeakWebQueryException;

class TeamSpeakService extends AbstractTeamSpeakService
{
    /**
     * @return Client[]
     */
    public function getClientList(Server $server): array
    {
        $url = "/1/clientlist?-uid";

        $response = $this->makeRequest($server, $url);

        if (null === $liveClients = $this->checkResponseForError($response)) {
            throw new TeamSpeakWebQueryException('No data returned while expected');
        }

        $clients = [];
        foreach ($liveClients as $client) {
            $clients[] = new Client(
                (int) $client['clid'],
                (int) $client['client_database_id'],
                $client['client_nickname'],
                $client['client_unique_identifier']
            );
        }

        return $clients;
    }

    public function setServerName(Server $server, string $name): void
    {
        $url = "/1/serveredit?virtualserver_name=$name";

        $response = $this->makeRequest($server, $url);

        $this->checkResponseForError($response);
    }

    public function setDefaultChannelName(Server $server, string $name): void
    {
        $url = "/1/channellist?-flags";

        $response = $this->makeRequest($server, $url);

        /** @var array<int, array<string, string>> $channels */
        $channels = $this->checkResponseForError($response);

        foreach ($channels as $channel) {
            if ($channel['channel_flag_default'] === '1') {
                $url = "/1/channeledit?cid={$channel['cid']}&channel_name=$name";

                $response = $this->makeRequest($server, $url);
                $this->checkResponseForError($response);

                break;
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Server;

class TeamSpeakService extends AbstractTeamSpeakService
{
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

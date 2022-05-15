<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\KeyHistoryRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KeyHistoryRepository::class)]
class KeyHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Server::class)]
    private Server $server;

    #[ORM\Column(type: Types::STRING)]
    private string $apiKey;

    #[ORM\Column(type: Types::STRING)]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $created;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function setServer(Server $server): void
    {
        $this->server = $server;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}

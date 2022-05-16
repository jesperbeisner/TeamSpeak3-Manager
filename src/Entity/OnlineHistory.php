<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OnlineHistoryRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OnlineHistoryRepository::class)]
#[ORM\Index(columns: ['uuid'])]
class OnlineHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Server::class)]
    private Server $server;

    #[ORM\Column(type: Types::STRING)]
    private string $uuid;

    #[ORM\Column(type: Types::STRING)]
    private string $username;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $startSession;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $endSession = null;

    public function __construct()
    {
        $this->startSession = new DateTime();
    }

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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getStartSession(): DateTime
    {
        return $this->startSession;
    }

    public function setStartSession(DateTime $startSession): void
    {
        $this->startSession = $startSession;
    }

    public function getEndSession(): ?DateTime
    {
        return $this->endSession;
    }

    public function setEndSession(?DateTime $endSession): void
    {
        $this->endSession = $endSession;
    }
}

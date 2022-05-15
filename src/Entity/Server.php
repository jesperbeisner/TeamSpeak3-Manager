<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServerRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
#[ORM\UniqueConstraint(name: 'container_name_index', columns: ['container_name'])]
#[ORM\UniqueConstraint(name: 'port_index', columns: ['port'])]
class Server
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $port;

    #[ORM\Column(type: Types::INTEGER)]
    private int $webQueryPort;

    #[ORM\Column(type: Types::STRING)]
    private string $containerName;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $apiKey = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $started = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $synchronized = true;

    public function reset(): void
    {
        $this->apiKey = null;
        $this->token = null;
        $this->started = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getWebQueryPort(): int
    {
        return $this->webQueryPort;
    }

    public function setWebQueryPort(int $webQueryPort): void
    {
        $this->webQueryPort = $webQueryPort;
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function setContainerName(string $containerName): void
    {
        $this->containerName = $containerName;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getStarted(): ?DateTime
    {
        return $this->started;
    }

    public function setStarted(?DateTime $started): void
    {
        $this->started = $started;
    }

    public function isSynchronized(): bool
    {
        return $this->synchronized;
    }

    public function setSynchronized(bool $synchronized): void
    {
        $this->synchronized = $synchronized;
    }
}

<?php

declare(strict_types=1);

namespace App\Data;

class Client
{
    public function __construct(
        public readonly int $id,
        public readonly int $databaseId,
        public readonly string $nickname,
        public readonly string $uuid,
    ) {
    }
}

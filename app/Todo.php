<?php

declare(strict_types=1);

namespace App;

class Todo
{
    public function __construct(
        public readonly int $id,
        public string $title,
        public string $description = '',
        public bool $completed = false,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
    }
}
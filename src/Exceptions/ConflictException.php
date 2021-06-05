<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Exceptions;

use RuntimeException;

class ConflictException extends RuntimeException
{
    public static function create(string $state): self
    {
        return new self(
            "Attempted to add multiple '$state' calls. Only one allowed"
        );
    }
}

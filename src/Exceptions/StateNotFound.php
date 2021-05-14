<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Exceptions;

use RuntimeException;

class StateNotFound extends RuntimeException
{
    public static function create(string $state, string $model): self
    {
        return new self(
            "Attempted to reach '$model.$state' but it does not exists"
        );
    }
}

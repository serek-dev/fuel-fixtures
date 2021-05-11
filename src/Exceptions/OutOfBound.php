<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Exceptions;

use OutOfBoundsException;

class OutOfBound extends OutOfBoundsException
{
    public static function create(string $property): self
    {
        return new self(
            "Attempted to reach '$property' but it does not exists"
        );
    }
}

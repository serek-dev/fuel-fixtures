<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\DependencyInjection;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class NullObjectContainer implements ContainerInterface
{
    public function get(string $id)
    {
        throw new class extends RuntimeException implements NotFoundExceptionInterface {
        };
    }

    public function has(string $id): bool
    {
        return false;
    }
}

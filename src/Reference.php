<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures;

use Stwarog\FuelFixtures\Fuel\FactoryContract;

final class Reference
{
    private FactoryContract $factory;
    private string $property;

    public function __construct(string $property, FactoryContract $factory)
    {
        $this->property = $property;
        $this->factory = $factory;
    }

    public static function for(string $property, FactoryContract $factory): self
    {
        return new self($property, $factory);
    }

    /**
     * @return array{0: string, 1: FactoryContract}
     */
    public function toArray(): array
    {
        return [$this->property, $this->factory];
    }
}

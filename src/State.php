<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures;

final class State
{
    private string $state;
    /** @var array<string, mixed> */
    private array $attributes;

    /**
     * @param string $state
     * @param array<string, mixed> $attributes
     */
    public function __construct(string $state, array $attributes = [])
    {
        $this->state = $state;
        $this->attributes = $attributes;
    }

    /**
     * @param string $state
     * @param array<string, mixed> $attributes
     * @return static
     */
    public static function for(string $state, array $attributes = []): self
    {
        return new self($state, $attributes);
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function __toString(): string
    {
        return $this->state;
    }
}

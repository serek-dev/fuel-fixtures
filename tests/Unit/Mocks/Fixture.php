<?php

declare(strict_types=1);

namespace Tests\Unit\Mocks;

use Stwarog\FuelFixtures\Fuel\Factory;

final class Fixture extends Factory
{
    public static function getClass(): string
    {
        return ModelImitation::class;
    }

    public function getDefaults(): array
    {
        return [
            'id' => 'id',
            'status' => 'status',
            'body' => 'body',
            'relation' => null
        ];
    }

    public function getStates(): array
    {
        return [
            'fake' => static function (ModelImitation $model, array $attributes = []) {
                $model->body = 'fake';
                $model->status = 'status from fake';
            },
            'fake2' => static function (ModelImitation $model, array $attributes = []) {
                $model->body = 'fake2';
            },
            'factory' => ['relation', $this],
            'factory_reference' => $this->reference('relation', Fixture::class),
            'factory_many' => ['relation_many', $this],
            'not_existing_reference' => function (ModelImitation $model, array $attributes = []) {
                $this->fixture('fake');
            },
        ];
    }
}

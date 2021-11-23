<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Events;

/**
 * Class ModelCreated
 * Should be dispatcher in FactoryContract > makeOne
 */
final class ModelPrepared
{
    public const NAME = 'model.prepared';

    private object $model;

    public function __construct(object $model)
    {
        $this->model = $model;
    }

    public function getModel(): object
    {
        return $this->model;
    }
}

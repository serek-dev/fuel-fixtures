<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Events;

/**
 * Class BeforePrepared
 * Should be dispatched in FactoryContract > makeOne
 */
final class BeforePrepared
{
    public const NAME = 'model.before.prepared';

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

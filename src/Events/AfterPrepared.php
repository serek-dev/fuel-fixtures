<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Events;

/**
 * Class AfterPrepared
 * Should be dispatched in FactoryContract > makeOne
 */
final class AfterPrepared
{
    public const NAME = 'model.after.prepared';

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

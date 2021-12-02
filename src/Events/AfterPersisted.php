<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Events;

/**
 * Class AfterPersisted
 * Should be dispatched in PersistenceContract > persist
 */
final class AfterPersisted
{
    public const NAME = 'model.after.persisted';

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

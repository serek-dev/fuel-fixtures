<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Exception;
use Orm\Model;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Events\AfterPersisted;
use Stwarog\FuelFixtures\Events\BeforePersisted;
use Stwarog\FuelFixtures\Events\NullObjectDispatcher;

final class FuelPersistence implements PersistenceContract
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher ?? new NullObjectDispatcher();
    }

    /**
     * @param Model<array> ...$models
     * @throws Exception
     */
    public function persist(Model ...$models): void
    {
        foreach ($models as $model) {
            $this->dispatcher->dispatch(new BeforePersisted($model));
            $model->save();
            $this->dispatcher->dispatch(new AfterPersisted($model));
        }
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}

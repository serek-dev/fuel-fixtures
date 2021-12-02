<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Exception;
use Orm\Model;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Events\AfterPersisted;
use Stwarog\FuelFixtures\Events\BeforePersisted;
use Stwarog\FuelFixtures\Events\NullObjectDispatcher;
use Stwarog\UowFuel\FuelEntityManager;

final class UowPersistence implements PersistenceContract
{
    private FuelEntityManager $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(FuelEntityManager $em, ?EventDispatcherInterface $dispatcher = null)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher ?? new NullObjectDispatcher();
    }

    /**
     * @param Model<mixed> ...$models
     * @throws Exception
     */
    public function persist(Model ...$models): void
    {
        foreach ($models as $model) {
            $this->dispatcher->dispatch(new BeforePersisted($model));
            $this->em->save($model);
            $this->dispatcher->dispatch(new AfterPersisted($model));
        }
        $this->em->flush();
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}

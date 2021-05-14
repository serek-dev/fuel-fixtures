<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Exception;
use Orm\Model;
use Stwarog\UowFuel\FuelEntityManager;

class UowPersistence implements PersistenceContract
{
    private FuelEntityManager $em;

    public function __construct(FuelEntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Model<mixed> ...$models
     * @throws Exception
     */
    public function persist(Model ...$models): void
    {
        foreach ($models as $model) {
            $this->em->save($model);
        }
        $this->em->flush();
    }
}

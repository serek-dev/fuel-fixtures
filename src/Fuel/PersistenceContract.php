<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Orm\Model;

interface PersistenceContract
{
    /**
     * Class should persist & flush changes into the DB source.
     * @param Model<array> ...$proxy
     */
    public function persist(Model ...$proxy): void;
}

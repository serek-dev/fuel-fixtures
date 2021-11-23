<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Events;

use Psr\EventDispatcher\EventDispatcherInterface;

final class NullObjectDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): object
    {
        return $event;
    }
}

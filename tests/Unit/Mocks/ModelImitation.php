<?php

declare(strict_types=1);

namespace Tests\Unit\Mocks;

use Orm\Model;

class ModelImitation extends Model
{
    public const PROPERTIES = ['id', 'status', 'body'];
}

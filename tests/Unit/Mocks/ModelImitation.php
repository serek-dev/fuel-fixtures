<?php

declare(strict_types=1);

namespace Tests\Unit\Mocks;

use Orm\Model;

/**
 * @property int $id
 * @property string $status
 * @property string $body
 * @property int $related_id
 * @property ?object $relation
 * @property array<object> $relation_many
 */
class ModelImitation extends Model
{
    public const PROPERTIES = ['id', 'status', 'body', 'related_id'];
}

<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

interface EntityPersisterInterface
{
    public function insert(object $entity): void;

    /** @var array<array-key, object> $entities */
    public function bulkInsert(array $entities): void;
}
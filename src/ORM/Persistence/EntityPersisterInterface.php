<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Entity\ManagedEntity;

interface EntityPersisterInterface
{
    public function insert(ManagedEntity $managedEntity): int;
    /** @param array<string, mixed> $changeSet */
    public function update(ManagedEntity $managedEntity, array $changeSet): void;
    public function delete(ManagedEntity $managedEntity): void;
}

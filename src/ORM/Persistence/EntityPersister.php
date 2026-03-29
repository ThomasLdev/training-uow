<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Entity\ManagedEntity;

// TODO : wrap everything in a transaction
final readonly class EntityPersister implements EntityPersisterInterface
{
    public function insert(ManagedEntity $managedEntity): int
    {
        // TODO : implement the insert logic from the metadata INSERT INTO $tableName VALUES($fields)
        return 1;
    }

    /** @param array<string, mixed> $changeSet */
    public function update(ManagedEntity $managedEntity, array $changeSet): void
    {
        // TODO: Implement update() method. UPDATE TABLE SET $columns = $VALUES WHERE $primaryKey = $primaryKeyValue
    }

    public function delete(ManagedEntity $managedEntity): void
    {
        // TODO: Implement delete() method. DELETE FROM $tableName WHERE $primaryKey = $primaryKeyValue
    }
}

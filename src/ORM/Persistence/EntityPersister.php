<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Database\PdoWrapper;
use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Entity\ManagedEntity;
use TrainingUow\ORM\Entity\Model\ChangeSet;
use TrainingUow\ORM\Persistence\Exception\EntityPersistenceException;

final readonly class EntityPersister implements EntityPersisterInterface
{
    public function __construct(private PdoWrapper $pdoWrapper) {}

    public function insert(ManagedEntity $managedEntity, ChangeSet $changeSet): string
    {
        if (EntityState::New !== $managedEntity->getEntityState()) {
            throw EntityPersistenceException::cannotInsertNotNewEntity(
                $managedEntity->getEntity()::class,
                $managedEntity->getEntityState(),
            );
        }

        $metadata = $managedEntity->getMetadata();

        $sql = <<<SQL
            INSERT INTO {$metadata->tableName} ({$changeSet->getColumns()})
            VALUES ({$changeSet->getPlaceholders()})
        SQL;

        $this->pdoWrapper->getPdo()->prepare($sql)->execute($changeSet->getValues());

        return $this->pdoWrapper->getLastInsertId($metadata);
    }

    public function update(ManagedEntity $managedEntity, ChangeSet $changeSet): void
    {
        // TODO: Implement update() method. UPDATE TABLE SET $columns = $VALUES WHERE $primaryKey = $primaryKeyValue
    }

    public function delete(ManagedEntity $managedEntity): void
    {
        // TODO: Implement delete() method. DELETE FROM $tableName WHERE $primaryKey = $primaryKeyValue
    }
}

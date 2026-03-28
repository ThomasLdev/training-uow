<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\Exception\EntityPersistenceException;
use PDO;
use ReflectionClass;

final readonly class EntityPersister implements EntityPersisterInterface
{
    private EntityMetadata $metadata;

    public function __construct(private PDO $pdo)
    {
    }

    public function insert(object $entity): void
    {
        $this->metadata = new EntityMetadataFactory($entity)->createFromEntity();

        // TODO : implement the insert logic from the metadata
    }

    private function setEntityId(object $entity): void
    {
        $sequence = sprintf('%s_%s_seq', $this->metadata->tableName, $this->metadata->primaryKey);
        $lastId = $this->pdo->lastInsertId($sequence);

        if (false === $lastId) {
            throw EntityPersistenceException::failToGetLastIdFromSequence($sequence);
        }

        $reflexion = new ReflectionClass($entity);

        if (!$reflexion->hasProperty('id')) {
            throw EntityPersistenceException::failToSetIdOnEntity($this->metadata->entityFQCN);
        }

        $reflexion->setStaticPropertyValue('id', (integer) $lastId);
    }
}
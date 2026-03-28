<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use PDO;
use ReflectionClass;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\Exception\EntityPersistenceException;

final readonly class EntityPersister implements EntityPersisterInterface
{
    public function __construct(private PDO $pdo, private EntityMetadataFactory $metadataFactory) {}

    // TODO : try real bulk insert with a single SQL query
    /** @inheritDoc */
    public function bulkInsert(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->insert($entity);
        }
    }

    public function insert(object $entity): void
    {
        $this->metadataFactory->createFromEntity($entity);
        // TODO : implement the insert logic from the metadata
    }

    /** @phpstan-ignore method.unused (will be used when insert logic is implemented) */
    private function setEntityId(object $entity, EntityMetadata $metadata): void
    {
        $sequence = sprintf('%s_%s_seq', $metadata->tableName, $metadata->primaryKey);
        $lastId = $this->pdo->lastInsertId($sequence);

        if (false === $lastId) {
            throw EntityPersistenceException::failToGetLastIdFromSequence($sequence);
        }

        $reflexion = new ReflectionClass($entity);

        if (!$reflexion->hasProperty($metadata->primaryKey)) {
            throw EntityPersistenceException::failToSetIdOnEntity($metadata->entityFQCN);
        }

        $reflexion->getProperty($metadata->primaryKey)->setValue($entity, $lastId);
    }
}

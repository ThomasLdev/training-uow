<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use ReflectionException;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\Exception\EntityPersistenceException;

final readonly class EntityPersister implements EntityPersisterInterface
{
    private EntityMetadataFactory $metadataFactory;

    public function __construct()
    {
        $this->metadataFactory = new EntityMetadataFactory();
    }

    // TODO : real bulk insert with a single SQL query
    /** @inheritDoc */
    public function bulkInsert(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->insert($entity);
        }
    }

    public function insert(object $entity): void
    {
        $className = $entity::class;

        try {
            $classMetadata = $this->metadataFactory->fromClassName($className);
        } catch (ReflectionException) {
            throw EntityPersistenceException::failedToGetClassReflection($className);
        }

        // TODO : implement the insert logic from the metadata
    }
}

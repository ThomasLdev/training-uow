<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;

final readonly class EntityPersister implements EntityPersisterInterface
{
    public function __construct(private EntityMetadataFactory $metadataFactory) {}

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
}

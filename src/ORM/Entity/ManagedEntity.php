<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;

#[Exclude]
final class ManagedEntity
{
    /**
     * @param array<string, mixed> $originalData
     */
    public function __construct(
        private readonly object $entity,
        private EntityState $state,
        private array $originalData,
        private readonly EntityMetadata $metadata,
    ) {}

    public function getEntityState(): EntityState
    {
        return $this->state;
    }

    public function setEntityState(EntityState $entityState): self
    {
        $this->state = $entityState;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    /** @param array<string, mixed> $originalData */
    public function setOriginalData(array $originalData): self
    {
        $this->originalData = $originalData;

        return $this;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getMetadata(): EntityMetadata
    {
        return $this->metadata;
    }

    /**
     * @param array<string, mixed> $currentValues keyed by propertyName
     * @return array<string, mixed> keyed by columnName
     */
    public function getColumnValuesPairs(array $currentValues): array
    {
        $map = [];

        foreach ($this->metadata->fieldsMetadata as $field) {
            $map[$field->columnName] = $currentValues[$field->propertyName];
        }

        return $map;
    }
}

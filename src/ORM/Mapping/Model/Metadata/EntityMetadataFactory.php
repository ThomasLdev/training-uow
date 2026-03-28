<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use ReflectionAttribute;
use ReflectionClass;
use TrainingUow\ORM\Mapping\Attributes\Table;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\Exception\ExtractionException;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\PropertyExtractor;

final class EntityMetadataFactory
{
    private array $cache = [];

    private ReflectionClass $reflection;

    private string $entityFQCN;

    public function __construct(private readonly object $entity)
    {
        $this->reflection = new ReflectionClass($this->entity);
        $this->entityFQCN = $this->entity::class;
    }

    public function createFromEntity(): EntityMetadata
    {
        if (array_key_exists($this->entityFQCN, $this->cache)) {
            return $this->cache[$this->entityFQCN];
        }

        $propertyAttributes = new PropertyExtractor($this->reflection)->extract();
        $metadata = new EntityMetadata(
            entityFQCN: $this->entityFQCN,
            tableName: $this->getEntityTableName(),
            primaryKey: $propertyAttributes->primaryKey,
            fieldsMetadata: $propertyAttributes->fieldsMetadata,
        );

        $this->cache[$this->entityFQCN] = $metadata;

        return $metadata;
    }

    private function getEntityTableName(): string
    {
        $table = $this->reflection->getAttributes(Table::class)[0];

        if (!$table instanceof ReflectionAttribute) {
            throw ExtractionException::cannotFindTableName($this->entityFQCN);
        }

        return $table->getArguments()['name'];
    }
}
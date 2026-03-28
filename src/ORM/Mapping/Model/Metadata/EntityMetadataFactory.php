<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use ReflectionClass;
use TrainingUow\ORM\Mapping\Attributes\Table;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\Exception\ExtractionException;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\PropertyExtractor;

final class EntityMetadataFactory
{
    private static array $cache = [];

    private ReflectionClass $reflection;

    private string $entityFQCN;

    public function createFromEntity(object $entity): EntityMetadata
    {
        $this->reflection = new ReflectionClass($entity);
        $this->entityFQCN = $entity::class;

        if (array_key_exists($this->entityFQCN, self::$cache)) {
            return self::$cache[$this->entityFQCN];
        }

        $propertyAttributes = new PropertyExtractor($this->reflection)->extract();
        $metadata = new EntityMetadata(
            entityFQCN: $this->entityFQCN,
            tableName: $this->getEntityTableName(),
            primaryKey: $propertyAttributes->primaryKey,
            fieldsMetadata: $propertyAttributes->fieldsMetadata,
        );

        self::$cache[$this->entityFQCN] = $metadata;

        return $metadata;
    }

    private function getEntityTableName(): string
    {
        $table = $this->reflection->getAttributes(Table::class)[0]->newInstance();

        if (!$table instanceof Table) {
            throw ExtractionException::cannotFindTableName($this->entityFQCN);
        }

        return $table->name;
    }
}
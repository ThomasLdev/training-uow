<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use ReflectionClass;
use TrainingUow\ORM\Mapping\Attributes\Table;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\Exception\ExtractionException;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\PropertyExtractor;

final class EntityMetadataFactory
{
    /** @var array<string, EntityMetadata> */
    private static array $cache = [];

    public function createFromEntity(object $entity): EntityMetadata
    {
        $reflection = new ReflectionClass($entity);
        $entityFQCN = $reflection->getName();

        if (array_key_exists($entityFQCN, self::$cache)) {
            return self::$cache[$entityFQCN];
        }

        $propertyAttributes = new PropertyExtractor($reflection)->extract();
        $metadata = new EntityMetadata(
            entityFQCN: $entityFQCN,
            tableName: $this->getEntityTableName($reflection),
            primaryKey: $propertyAttributes->primaryKey,
            fieldsMetadata: $propertyAttributes->fieldsMetadata,
        );

        self::$cache[$entityFQCN] = $metadata;

        return $metadata;
    }

    /** @param ReflectionClass<object> $reflection */
    private function getEntityTableName(ReflectionClass $reflection): string
    {
        $table = $reflection->getAttributes(Table::class)[0] ?? null;

        if (null === $table) {
            throw ExtractionException::cannotFindTableName($reflection->getName());
        }

        return $table->newInstance()->name;
    }
}

<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use ReflectionClass;
use ReflectionException;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributeExtractor;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;
use TrainingUow\ORM\Mapping\Model\Metadata\Exception\EntityMetadataException;

final class EntityMetadataFactory
{
    /** @var array<string, EntityMetadata> */
    private static array $cache = [];

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public function fromClassName(string $className): EntityMetadata
    {
        if (array_key_exists($className, self::$cache)) {
            return self::$cache[$className];
        }

        $reflection = new ReflectionClass($className);

        $propertyAttributes = new EntityAttributeExtractor($reflection)->extract();

        $this->validateMetadata($propertyAttributes, $className);

        $metadata = new EntityMetadata(
            entityFQCN: $className,
            tableName: $propertyAttributes->tableName,
            primaryKey: $propertyAttributes->primaryKey,
            fieldsMetadata: $propertyAttributes->fieldsMetadata,
        );

        self::$cache[$className] = $metadata;

        return $metadata;
    }

    private function validateMetadata(EntityAttributes $entityAttributes, string $className): void
    {
        if ('' === $entityAttributes->tableName) {
            throw EntityMetadataException::tableNameNotSpecified($className);
        }

        if ('' === $entityAttributes->primaryKey) {
            throw EntityMetadataException::primaryKeyNotSpecified($className);
        }

        if ([] === $entityAttributes->fieldsMetadata) {
            throw EntityMetadataException::noMappedFieldsFound($className);
        }
    }
}

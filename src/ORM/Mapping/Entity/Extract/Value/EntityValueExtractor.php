<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Value;

use ReflectionClass;
use ReflectionException;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\Exception\EntityValueExtractionException;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;

class EntityValueExtractor
{
    public function extract(object $object, EntityMetadata $metadata): array
    {
        $reflection = new ReflectionClass($object);
        $snapshot = [];

        foreach ($metadata->fieldsMetadata as $fieldMetadata) {
            try {
                $snapshot[$fieldMetadata->propertyName] = $reflection
                    ->getProperty($fieldMetadata->propertyName)
                    ->getValue($object)
                ;
            } catch (ReflectionException) {
                throw EntityValueExtractionException::couldNotExtractValueFromProperty(
                    $object::class,
                    $fieldMetadata->propertyName
                );
            }
        }

        return $snapshot;
    }
}
<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class EntityMetadataException extends RuntimeException
{
    public static function tableNameNotSpecified(string $entityName): self
    {
        return new self(
            'No table name specified for entity "' . $entityName . '"',
        );
    }

    public static function primaryKeyNotSpecified(string $entityName): self
    {
        return new self(
            'No primary key specified for entity "' . $entityName . '"',
        );
    }

    public static function noMappedFieldsFound(string $entityName): self
    {
        return new self(
            'No mapped fields found for entity "' . $entityName . '"',
        );
    }
}

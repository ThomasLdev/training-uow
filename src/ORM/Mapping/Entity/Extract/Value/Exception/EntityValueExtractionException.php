<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Value\Exception;

use RuntimeException;

class EntityValueExtractionException extends RuntimeException
{
    public static function couldNotExtractValueFromProperty(string $entityClass, string $propertyName): self
    {
        return new self(
            sprintf('Could not extract value from property %s of entity name %s.', $propertyName, $entityClass)
        );
    }
}
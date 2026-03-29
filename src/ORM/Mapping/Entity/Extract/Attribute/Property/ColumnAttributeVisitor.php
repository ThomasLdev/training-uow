<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property;

use ReflectionAttribute;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

class ColumnAttributeVisitor implements PropertyAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool
    {
        return array_find($attributes, static fn(ReflectionAttribute $a): bool => $a->getName() === Column::class) instanceof ReflectionAttribute;
    }

    public function visit(ReflectionProperty $reflectionProperty, EntityAttributes $entityAttributes): void
    {
        $column = $reflectionProperty->getAttributes(Column::class)[0]->newInstance();

        $entityAttributes->addFieldMetadata(new FieldMetadata(
            propertyName : $reflectionProperty->getName(),
            columnName: $column->name,
            type: $column->type,
            length: $column->length,
            nullable: $column->nullable,
        ));
    }
}

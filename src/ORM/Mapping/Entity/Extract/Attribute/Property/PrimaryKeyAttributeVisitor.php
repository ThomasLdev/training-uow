<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property;

use ReflectionAttribute;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;

class PrimaryKeyAttributeVisitor implements PropertyAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool
    {
        return array_find($attributes, static fn(ReflectionAttribute $a): bool => $a->getName() === PrimaryKey::class) instanceof ReflectionAttribute;
    }

    public function visit(ReflectionProperty $reflectionProperty, EntityAttributes $entityAttributes): void
    {
        $entityAttributes->primaryKey = $reflectionProperty->getName();
    }
}

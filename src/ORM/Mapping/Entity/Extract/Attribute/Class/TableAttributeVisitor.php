<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Class;

use ReflectionAttribute;
use ReflectionClass;
use TrainingUow\ORM\Mapping\Attributes\Table;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;

class TableAttributeVisitor implements ClassAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool
    {
        return array_find($attributes, static fn(ReflectionAttribute $a): bool => $a->getName() === Table::class) instanceof ReflectionAttribute;
    }

    /** @param ReflectionClass<object> $reflectionClass */
    public function visit(ReflectionClass $reflectionClass, EntityAttributes $entityAttributes): void
    {
        $table = $reflectionClass->getAttributes(Table::class)[0]->newInstance();

        $entityAttributes->tableName = $table->name;
    }
}

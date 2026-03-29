<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Class;

use ReflectionAttribute;
use ReflectionClass;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;

interface ClassAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool;

    /** @param ReflectionClass<object> $reflectionClass */
    public function visit(ReflectionClass $reflectionClass, EntityAttributes $entityAttributes): void;
}

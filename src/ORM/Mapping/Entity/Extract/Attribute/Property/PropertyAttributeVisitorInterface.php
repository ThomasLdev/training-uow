<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property;

use ReflectionAttribute;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;

interface PropertyAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool;

    public function visit(ReflectionProperty $reflectionProperty, EntityAttributes $entityAttributes): void;
}

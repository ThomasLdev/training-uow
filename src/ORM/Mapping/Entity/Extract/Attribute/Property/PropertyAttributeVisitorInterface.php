<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property;

use ReflectionAttribute;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;

#[AutoconfigureTag]
interface PropertyAttributeVisitorInterface
{
    /** @param list<ReflectionAttribute<object>> $attributes */
    public function supports(array $attributes): bool;

    public function visit(ReflectionProperty $reflectionProperty, EntityAttributes $entityAttributes): void;
}

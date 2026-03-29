<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute;

use ReflectionClass;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Class\ClassAttributeVisitorRegistry;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property\PropertyAttributeVisitorRegistry;

final readonly class EntityAttributeExtractor
{
    /** @param ReflectionClass<object> $reflectionClass */
    public function __construct(
        private ReflectionClass $reflectionClass,
        private PropertyAttributeVisitorRegistry $propertyAttributeVisitors = new PropertyAttributeVisitorRegistry(),
        private ClassAttributeVisitorRegistry $classAttributeVisitors = new ClassAttributeVisitorRegistry(),
    ) {}

    public function extract(): EntityAttributes
    {
        $entityAttributes = new EntityAttributes();

        $this->visitClassAttributes($entityAttributes);

        foreach ($this->reflectionClass->getProperties() as $property) {
            $this->visitPropertyAttributes($property, $entityAttributes);
        }

        return $entityAttributes;
    }

    private function visitClassAttributes(EntityAttributes $attributes): void
    {
        $classAttributes = $this->reflectionClass->getAttributes();

        foreach ($this->classAttributeVisitors->get() as $visitor) {
            if ($visitor->supports($classAttributes)) {
                $visitor->visit($this->reflectionClass, $attributes);
            }
        }
    }

    private function visitPropertyAttributes(ReflectionProperty $property, EntityAttributes $attributes): void
    {
        $propertyAttributes = $property->getAttributes();

        foreach ($this->propertyAttributeVisitors->get() as $visitor) {
            if ($visitor->supports($propertyAttributes)) {
                $visitor->visit($property, $attributes);
            }
        }
    }
}

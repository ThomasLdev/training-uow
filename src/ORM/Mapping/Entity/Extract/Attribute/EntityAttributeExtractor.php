<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Class\ClassAttributeVisitorInterface;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property\PropertyAttributeVisitorInterface;

final readonly class EntityAttributeExtractor
{
    /**
     * @param iterable<ClassAttributeVisitorInterface> $classAttributeVisitors
     * @param iterable<PropertyAttributeVisitorInterface> $propertyAttributeVisitors
     */
    public function __construct(
        #[AutowireIterator(ClassAttributeVisitorInterface::class)]
        private iterable $classAttributeVisitors,
        #[AutowireIterator(PropertyAttributeVisitorInterface::class)]
        private iterable $propertyAttributeVisitors,
    ) {}

    /** @param ReflectionClass<object> $reflectionClass */
    public function extract(ReflectionClass $reflectionClass): EntityAttributes
    {
        $entityAttributes = new EntityAttributes();

        $this->visitClassAttributes($reflectionClass, $entityAttributes);

        foreach ($reflectionClass->getProperties() as $property) {
            $this->visitPropertyAttributes($property, $entityAttributes);
        }

        return $entityAttributes;
    }

    /** @param ReflectionClass<object> $reflectionClass */
    private function visitClassAttributes(ReflectionClass $reflectionClass, EntityAttributes $attributes): void
    {
        $classAttributes = $reflectionClass->getAttributes();

        foreach ($this->classAttributeVisitors as $visitor) {
            if ($visitor->supports($classAttributes)) {
                $visitor->visit($reflectionClass, $attributes);
            }
        }
    }

    private function visitPropertyAttributes(ReflectionProperty $property, EntityAttributes $attributes): void
    {
        $propertyAttributes = $property->getAttributes();

        foreach ($this->propertyAttributeVisitors as $visitor) {
            if ($visitor->supports($propertyAttributes)) {
                $visitor->visit($property, $attributes);
            }
        }
    }
}

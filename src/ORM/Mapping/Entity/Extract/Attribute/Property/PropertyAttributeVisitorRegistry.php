<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Property;

class PropertyAttributeVisitorRegistry
{
    /** @return list<PropertyAttributeVisitorInterface> */
    public function get(): array
    {
        return [
            new ColumnAttributeVisitor(),
            new PrimaryKeyAttributeVisitor(),
        ];
    }
}

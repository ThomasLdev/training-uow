<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute\Class;

class ClassAttributeVisitorRegistry
{
    /** @return list<ClassAttributeVisitorInterface> */
    public function get(): array
    {
        return [
            new TableAttributeVisitor(),
        ];
    }
}

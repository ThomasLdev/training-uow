<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Property\Extract;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

final readonly class PropertyExtractor
{
    private EntityAttributes $entityAttributes;

    public function __construct(private ReflectionClass $reflection)
    {
        $this->entityAttributes = new EntityAttributes();
    }

    public function extract(): EntityAttributes
    {
        foreach($this->reflection->getProperties() as $property) {
            $primaryKey = $property->getAttributes(PrimaryKey::class);

            if ([] !== $primaryKey) {
                $this->entityAttributes->primaryKey = $property->getName();

                continue;
            }

            $this->extractColumnAttribute($property);
        }

        return $this->entityAttributes;
    }

    private function extractColumnAttribute(ReflectionProperty $property): void
    {
        $column = $property->getAttributes(Column::class)[0] ?? null;

        if (!$column instanceof ReflectionAttribute) {
            return;
        }

        $arguments = $column->getArguments();

        $this->entityAttributes->addFieldMetadata(new FieldMetadata(
            name: $arguments['name'],
            type: $arguments['type'],
            length: $arguments['length'] ?? null,
            nullable: $arguments['nullable'] ?? false,
        ));
    }
}
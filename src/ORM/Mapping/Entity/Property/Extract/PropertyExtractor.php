<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Property\Extract;

use ReflectionClass;
use ReflectionProperty;
use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

final readonly class PropertyExtractor
{
    private EntityAttributes $entityAttributes;

    /** @param ReflectionClass<object> $reflection */
    public function __construct(private ReflectionClass $reflection)
    {
        $this->entityAttributes = new EntityAttributes();
    }

    public function extract(): EntityAttributes
    {
        foreach ($this->reflection->getProperties() as $property) {
            $this->setPrimaryKey($property);
            $this->extractColumnAttribute($property);
        }

        return $this->entityAttributes;
    }

    private function setPrimaryKey(ReflectionProperty $property): void
    {
        $primaryKey = $property->getAttributes(PrimaryKey::class)[0] ?? null;

        if (null === $primaryKey) {
            return;
        }

        $this->entityAttributes->primaryKey = $property->getName();
    }

    private function extractColumnAttribute(ReflectionProperty $property): void
    {
        $columnAttributes = $property->getAttributes(Column::class);

        if ([] === $columnAttributes) {
            return;
        }

        $column = $columnAttributes[0]->newInstance();

        $this->entityAttributes->addFieldMetadata(new FieldMetadata(
            propertyName : $property->getName(),
            columnName: $column->name,
            type: $column->type,
            length: $column->length,
            nullable: $column->nullable,
        ));
    }
}

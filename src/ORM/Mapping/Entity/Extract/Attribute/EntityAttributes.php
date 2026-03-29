<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Extract\Attribute;

use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

final class EntityAttributes
{
    public function __construct(
        public string $primaryKey = '',
        public string $tableName = '',
        /** @var array<string, FieldMetadata> */
        public array $fieldsMetadata = [],
    ) {}

    public function addFieldMetadata(FieldMetadata $fieldMetadata): void
    {
        $this->fieldsMetadata[$fieldMetadata->propertyName] = $fieldMetadata;
    }
}

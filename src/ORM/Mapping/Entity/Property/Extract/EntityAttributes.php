<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Property\Extract;

use LogicException;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

final class EntityAttributes
{
    public function __construct(
        public string $primaryKey = '',
        /** @var array<string, FieldMetadata> */
        public array $fieldsMetadata = [],
    ) {}

    public function addFieldMetadata(FieldMetadata $fieldMetadata): void
    {
        if (array_key_exists($fieldMetadata->propertyName, $this->fieldsMetadata)) {
            throw new LogicException('Field metadata already exists.');
        }

        $this->fieldsMetadata[$fieldMetadata->propertyName] = $fieldMetadata;
    }
}

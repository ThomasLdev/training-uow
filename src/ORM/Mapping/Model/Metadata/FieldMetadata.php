<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use TrainingUow\ORM\Mapping\Attributes\Enum\Type;

class FieldMetadata
{
    public function __construct(
        public string $propertyName,
        public string $columnName,
        public Type $type,
        public ?int $length = null,
        public bool $nullable = false,
    )
    {
    }
}
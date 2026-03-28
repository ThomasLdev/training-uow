<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Attributes;

use Attribute;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name,
        public Type $type,
        public bool $nullable = false,
        public ?int $length = null,
    ) {}
}

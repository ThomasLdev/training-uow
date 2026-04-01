<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Attributes;

use Attribute;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;

#[Exclude]
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

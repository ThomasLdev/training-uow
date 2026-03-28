<?php

declare(strict_types=1);

namespace Entity\Trait;

use ORM\Mapping\Attributes\Column;
use ORM\Mapping\Attributes\Enum\Type;

trait DescribableEntityTrait
{
    #[Column(name: 'title', type: Type::String, length: 255)]
    private(set) string $title = '';

    #[Column(name: 'description', type: Type::Text)]
    private(set) string $description = '';
}
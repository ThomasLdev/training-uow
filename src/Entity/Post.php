<?php

declare(strict_types=1);

namespace Entity;

use Entity\Trait\DescribableEntityTrait;
use Entity\Trait\PrimaryKeyTrait;
use ORM\Mapping\Attributes\Column;
use ORM\Mapping\Attributes\Enum\Type;
use ORM\Mapping\Attributes\Table;

#[Table(name: 'post')]
class Post
{
    use PrimaryKeyTrait;
    use DescribableEntityTrait;

    #[Column(name: 'content', type: Type::Text)]
    private(set) string $content = '';

    // TODO : create relation attributes
    private(set) ?Category $category = null;
}
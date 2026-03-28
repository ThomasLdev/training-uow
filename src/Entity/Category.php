<?php

declare(strict_types=1);

namespace Entity;

use Entity\Trait\DescribableEntityTrait;
use Entity\Trait\PrimaryKeyTrait;
use ORM\Mapping\Attributes\Table;

#[Table(name: 'category')]
class Category
{
    use PrimaryKeyTrait;
    use DescribableEntityTrait;
}
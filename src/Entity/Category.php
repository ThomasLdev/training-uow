<?php

declare(strict_types=1);

namespace TrainingUow\Entity;

use TrainingUow\Entity\Trait\DescribableEntityTrait;
use TrainingUow\Entity\Trait\PrimaryKeyTrait;
use TrainingUow\ORM\Mapping\Attributes\Table;

#[Table(name: 'category')]
class Category
{
    use PrimaryKeyTrait;
    use DescribableEntityTrait;
}

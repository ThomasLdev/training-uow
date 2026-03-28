<?php

declare(strict_types=1);

namespace Entity\Trait;

use ORM\Mapping\Attributes\PrimaryKey;

trait PrimaryKeyTrait
{
    #[PrimaryKey]
    private(set) ?int $id = null;
}
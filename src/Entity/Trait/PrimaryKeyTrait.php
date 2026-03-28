<?php

declare(strict_types=1);

namespace TrainingUow\Entity\Trait;

use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;

trait PrimaryKeyTrait
{
    #[PrimaryKey]
    #[Column(name: 'id', type: Type::Integer)]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

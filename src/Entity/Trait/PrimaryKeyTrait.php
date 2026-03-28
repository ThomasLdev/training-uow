<?php

declare(strict_types=1);

namespace TrainingUow\Entity\Trait;

use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;

trait PrimaryKeyTrait
{
    #[PrimaryKey]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

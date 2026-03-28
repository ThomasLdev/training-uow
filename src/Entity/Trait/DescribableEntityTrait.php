<?php

declare(strict_types=1);

namespace TrainingUow\Entity\Trait;

use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;

trait DescribableEntityTrait
{
    #[Column(name: 'title', type: Type::String, length: 255)]
    private string $title = '';

    #[Column(name: 'description', type: Type::Text)]
    private string $description = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}

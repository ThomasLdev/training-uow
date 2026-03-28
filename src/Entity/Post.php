<?php

declare(strict_types=1);

namespace TrainingUow\Entity;

use TrainingUow\Entity\Trait\DescribableEntityTrait;
use TrainingUow\Entity\Trait\PrimaryKeyTrait;
use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Attributes\Table;

#[Table(name: 'post')]
class Post
{
    use PrimaryKeyTrait;
    use DescribableEntityTrait;

    #[Column(name: 'content', type: Type::Text)]
    private string $content = '';

    // TODO : create relation attributes
    private ?Category $category = null;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}

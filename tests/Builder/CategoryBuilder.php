<?php

declare(strict_types=1);

namespace TrainingUow\Tests\Builder;

use TrainingUow\Entity\Category;

final class CategoryBuilder
{
    private string $title = 'Default category title';
    private string $description = 'Default category description';

    public static function aCategory(): self
    {
        return new self();
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function withDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function build(): Category
    {
        $category = new Category();
        $category->setTitle($this->title);
        $category->setDescription($this->description);

        return $category;
    }
}

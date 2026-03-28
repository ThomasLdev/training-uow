<?php

declare(strict_types=1);

namespace TrainingUow\Tests\Builder;

use TrainingUow\Entity\Category;
use TrainingUow\Entity\Post;

final class PostBuilder
{
    private string $title = 'Default post title';
    private string $description = 'Default post description';
    private string $content = 'Default post content';
    private ?Category $category = null;

    public static function aPost(): self
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

    public function withContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function withCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function build(): Post
    {
        $post = new Post();
        $post->setTitle($this->title);
        $post->setDescription($this->description);
        $post->setContent($this->content);
        $post->setCategory($this->category);

        return $post;
    }
}

<?php

declare(strict_types=1);

namespace TrainingUow\Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use TrainingUow\Tests\Builder\CategoryBuilder;
use TrainingUow\Tests\Builder\PostBuilder;

final class EntityManagerIntegrationTest extends DatabaseTestCase
{
    #[Test]
    public function it_inserts_a_post_into_the_database(): void
    {
        $post = PostBuilder::aPost()
            ->withTitle('Integration test post')
            ->withDescription('A description')
            ->withContent('Some content')
            ->build();

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $row = $this->pdo->query("SELECT * FROM post WHERE title = 'Integration test post'")->fetch();

        self::assertIsArray($row);
        self::assertSame('Integration test post', $row['title']);
        self::assertSame('A description', $row['description']);
        self::assertSame('Some content', $row['content']);
    }

    #[Test]
    public function it_assigns_a_database_generated_id(): void
    {
        $post = PostBuilder::aPost()->withTitle('ID test')->build();

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        self::assertIsInt($post->getId());
        self::assertGreaterThan(0, $post->getId());
    }

    #[Test]
    public function it_inserts_multiple_entities(): void
    {
        $post = PostBuilder::aPost()->withTitle('Post 1')->build();
        $category = CategoryBuilder::aCategory()->withTitle('Category 1')->build();

        $this->entityManager->persist($post);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $postCount = $this->pdo->query("SELECT COUNT(*) FROM post WHERE title = 'Post 1'")->fetchColumn();
        $categoryCount = $this->pdo->query("SELECT COUNT(*) FROM category WHERE title = 'Category 1'")->fetchColumn();

        self::assertSame(1, (int)$postCount);
        self::assertSame(1, (int)$categoryCount);
    }

    #[Test]
    public function it_does_not_persist_data_between_tests(): void
    {
        $count = $this->pdo->query("SELECT COUNT(*) FROM post WHERE title = 'Integration test post'")->fetchColumn();

        self::assertSame(0, (int)$count);
    }
}

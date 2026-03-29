<?php

declare(strict_types=1);

namespace TrainingUow\Tests\ORM\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TrainingUow\ORM\Entity\ChangeSet;
use TrainingUow\ORM\Entity\EntityManager;
use TrainingUow\ORM\Entity\ManagedEntity;
use TrainingUow\ORM\Entity\UnitOfWork;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\EntityValueExtractor;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\EntityPersisterInterface;
use TrainingUow\Tests\Builder\PostBuilder;

#[CoversClass(EntityManager::class)]
#[CoversClass(UnitOfWork::class)]
#[CoversClass(ManagedEntity::class)]
#[CoversClass(ChangeSet::class)]
final class EntityManagerTest extends TestCase
{
    private EntityManager $entityManager;
    private MockObject&EntityPersisterInterface $persister;

    protected function setUp(): void
    {
        $metadataFactory = new EntityMetadataFactory();

        $reflection = new ReflectionClass(EntityMetadataFactory::class);
        $reflection->setStaticPropertyValue('cache', []);

        $this->persister = $this->createMock(EntityPersisterInterface::class);

        $unitOfWork = new UnitOfWork($metadataFactory, new EntityValueExtractor(), $this->persister);
        $this->entityManager = new EntityManager($unitOfWork);
    }

    #[Test]
    public function it_inserts_a_new_entity_on_flush(): void
    {
        $post = PostBuilder::aPost()->withTitle('Hello')->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        self::assertSame(1, $post->getId());
    }

    #[Test]
    public function it_assigns_incrementing_ids_to_multiple_entities(): void
    {
        $post1 = PostBuilder::aPost()->withTitle('First')->build();
        $post2 = PostBuilder::aPost()->withTitle('Second')->build();

        $this->persister->expects($this->exactly(2))
            ->method('insert')
            ->willReturnOnConsecutiveCalls(1, 2);

        $this->entityManager->persist($post1);
        $this->entityManager->persist($post2);
        $this->entityManager->flush();

        self::assertSame(1, $post1->getId());
        self::assertSame(2, $post2->getId());
    }

    #[Test]
    public function it_does_not_insert_twice_when_persisting_same_entity_twice(): void
    {
        $post = PostBuilder::aPost()->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);

        $this->entityManager->persist($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    #[Test]
    public function it_does_not_insert_or_update_on_flush_without_changes_after_first_flush(): void
    {
        $post = PostBuilder::aPost()->withTitle('Stable')->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);
        $this->persister->expects($this->never())->method('update');

        $this->entityManager->persist($post);
        $this->entityManager->flush();
        $this->entityManager->flush();
    }

    #[Test]
    public function it_detects_changes_and_updates_on_second_flush(): void
    {
        $post = PostBuilder::aPost()->withTitle('Original')->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);
        $this->persister->expects($this->once())
            ->method('update')
            ->with(
                $this->isInstanceOf(ManagedEntity::class),
                $this->callback(function (array $changeSet): bool {
                    return $changeSet['title'] === 'Modified' && !array_key_exists('content', $changeSet);
                }),
            );

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $post->setTitle('Modified');
        $this->entityManager->flush();
    }

    #[Test]
    public function it_removes_a_new_entity_without_calling_persister(): void
    {
        $post = PostBuilder::aPost()->build();

        $this->persister->expects($this->never())->method('insert');
        $this->persister->expects($this->never())->method('delete');

        $this->entityManager->persist($post);
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    #[Test]
    public function it_deletes_a_managed_entity_on_flush(): void
    {
        $post = PostBuilder::aPost()->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);
        $this->persister->expects($this->once())->method('delete');

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    #[Test]
    public function it_re_manages_a_removed_entity_when_persisted_again(): void
    {
        $post = PostBuilder::aPost()->build();

        $this->persister->expects($this->once())->method('insert')->willReturn(1);
        $this->persister->expects($this->never())->method('delete');

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->entityManager->remove($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    #[Test]
    public function it_ignores_remove_on_non_managed_entity(): void
    {
        $post = PostBuilder::aPost()->build();

        $this->persister->expects($this->never())->method('insert');
        $this->persister->expects($this->never())->method('delete');

        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    #[Test]
    public function it_does_nothing_on_flush_without_persisted_entities(): void
    {
        $this->persister->expects($this->never())->method('insert');
        $this->persister->expects($this->never())->method('update');
        $this->persister->expects($this->never())->method('delete');

        $this->entityManager->flush();
    }
}

<?php

declare(strict_types=1);

namespace TrainingUow\Tests\ORM\Entity\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Entity\ManagedEntity;
use TrainingUow\ORM\Entity\Model\ChangeSet;
use TrainingUow\ORM\Entity\Model\ChangeSetFactory;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

#[CoversClass(ChangeSetFactory::class)]
#[CoversClass(ChangeSet::class)]
final class ChangeSetFactoryTest extends TestCase
{
    private ChangeSetFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ChangeSetFactory();
    }

    #[Test]
    public function it_returns_all_non_pk_values_for_a_new_entity(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::New,
            originalData: ['id' => null, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => null,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        self::assertSame([
            'title' => 'Hello',
            'content' => 'World',
        ], $changeSet->getValues());
    }

    #[Test]
    public function it_excludes_primary_key_from_changeset(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::New,
            originalData: ['id' => null, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => 42,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        self::assertArrayNotHasKey('id', $changeSet->getValues());
    }

    #[Test]
    public function it_returns_only_changed_values_for_a_managed_entity(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::Managed,
            originalData: ['id' => 1, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => 1,
            'title' => 'Updated',
            'description' => 'World',
        ]);

        self::assertSame(['title' => 'Updated'], $changeSet->getValues());
    }

    #[Test]
    public function it_returns_empty_changeset_when_managed_entity_has_no_changes(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::Managed,
            originalData: ['id' => 1, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => 1,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        self::assertSame([], $changeSet->getValues());
    }

    #[Test]
    public function it_maps_property_names_to_column_names(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::New,
            originalData: ['id' => null, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => null,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        $columns = array_keys($changeSet->getValues());

        self::assertSame(['title', 'content'], $columns);
    }

    #[Test]
    public function it_detects_all_changes_for_managed_entity_with_multiple_changes(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::Managed,
            originalData: ['id' => 1, 'title' => 'Old title', 'description' => 'Old desc'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => 1,
            'title' => 'New title',
            'description' => 'New desc',
        ]);

        self::assertSame([
            'title' => 'New title',
            'content' => 'New desc',
        ], $changeSet->getValues());
    }

    #[Test]
    public function it_generates_correct_sql_columns_string(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::New,
            originalData: ['id' => null, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => null,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        self::assertSame('title,content', $changeSet->getColumns());
    }

    #[Test]
    public function it_generates_correct_sql_placeholders(): void
    {
        $managedEntity = $this->createManagedEntity(
            state: EntityState::New,
            originalData: ['id' => null, 'title' => 'Hello', 'description' => 'World'],
        );

        $changeSet = $this->factory->get($managedEntity, [
            'id' => null,
            'title' => 'Hello',
            'description' => 'World',
        ]);

        self::assertSame(':title, :content', $changeSet->getPlaceholders());
    }

    /**
     * @param array<string, mixed> $originalData
     */
    private function createManagedEntity(EntityState $state, array $originalData): ManagedEntity
    {
        $metadata = new EntityMetadata(
            entityFQCN: stdClass::class,
            tableName: 'test_table',
            primaryKey: 'id',
            fieldsMetadata: [
                'id' => new FieldMetadata(propertyName: 'id', columnName: 'id', type: Type::Integer),
                'title' => new FieldMetadata(propertyName: 'title', columnName: 'title', type: Type::String, length: 255),
                'description' => new FieldMetadata(propertyName: 'description', columnName: 'content', type: Type::Text),
            ],
        );

        return new ManagedEntity(
            entity: new stdClass(),
            state: $state,
            originalData: $originalData,
            metadata: $metadata,
        );
    }
}

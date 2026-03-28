<?php

declare(strict_types=1);

namespace TrainingUow\Tests\ORM\Mapping\Model\Metadata;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\EntityAttributes;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\Exception\ExtractionException;
use TrainingUow\ORM\Mapping\Entity\Property\Extract\PropertyExtractor;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;
use TrainingUow\Tests\Builder\CategoryBuilder;
use TrainingUow\Tests\Builder\PostBuilder;

#[CoversClass(EntityMetadataFactory::class)]
#[CoversClass(EntityMetadata::class)]
#[CoversClass(FieldMetadata::class)]
#[CoversClass(PropertyExtractor::class)]
#[CoversClass(EntityAttributes::class)]
#[CoversClass(ExtractionException::class)]
final class EntityMetadataFactoryTest extends TestCase
{
    private EntityMetadataFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new EntityMetadataFactory();

        $reflection = new ReflectionClass(EntityMetadataFactory::class);
        $reflection->setStaticPropertyValue('cache', []);
    }

    #[Test]
    #[DataProvider('entityProvider')]
    public function it_extracts_table_name(object $entity, string $expectedTableName): void
    {
        $metadata = $this->factory->createFromEntity($entity);

        self::assertSame($expectedTableName, $metadata->tableName);
    }

    #[Test]
    #[DataProvider('entityProvider')]
    public function it_extracts_entity_fqcn(object $entity, string $expectedTableName): void
    {
        $metadata = $this->factory->createFromEntity($entity);

        self::assertSame($entity::class, $metadata->entityFQCN);
    }

    #[Test]
    #[DataProvider('entityProvider')]
    public function it_extracts_primary_key(object $entity, string $expectedTableName): void
    {
        $metadata = $this->factory->createFromEntity($entity);

        self::assertSame('id', $metadata->primaryKey);
    }

    #[Test]
    #[DataProvider('entityWithFieldsProvider')]
    public function it_extracts_field_metadata(
        object $entity,
        string $propertyName,
        string $expectedColumnName,
        Type $expectedType,
        ?int $expectedLength,
        bool $expectedNullable,
    ): void {
        $metadata = $this->factory->createFromEntity($entity);

        self::assertArrayHasKey($propertyName, $metadata->fieldsMetadata);

        $field = $metadata->fieldsMetadata[$propertyName];
        self::assertInstanceOf(FieldMetadata::class, $field);
        self::assertSame($expectedColumnName, $field->columnName);
        self::assertSame($expectedType, $field->type);
        self::assertSame($expectedLength, $field->length);
        self::assertSame($expectedNullable, $field->nullable);
    }

    #[Test]
    public function it_caches_metadata_for_same_entity_class(): void
    {
        $post1 = PostBuilder::aPost()->withTitle('First')->build();
        $post2 = PostBuilder::aPost()->withTitle('Second')->build();

        $metadata1 = $this->factory->createFromEntity($post1);
        $metadata2 = $this->factory->createFromEntity($post2);

        self::assertSame($metadata1, $metadata2);
    }

    #[Test]
    public function it_returns_different_metadata_for_different_entity_classes(): void
    {
        $post = PostBuilder::aPost()->build();
        $category = CategoryBuilder::aCategory()->build();

        $postMetadata = $this->factory->createFromEntity($post);
        $categoryMetadata = $this->factory->createFromEntity($category);

        self::assertNotSame($postMetadata, $categoryMetadata);
        self::assertSame('post', $postMetadata->tableName);
        self::assertSame('category', $categoryMetadata->tableName);
    }

    #[Test]
    public function it_throws_when_entity_has_no_table_attribute(): void
    {
        $entity = new class {};

        $this->expectException(ExtractionException::class);

        $this->factory->createFromEntity($entity);
    }

    public static function entityProvider(): iterable
    {
        yield 'post entity' => [
            PostBuilder::aPost()->build(),
            'post',
        ];

        yield 'category entity' => [
            CategoryBuilder::aCategory()->build(),
            'category',
        ];
    }

    public static function entityWithFieldsProvider(): iterable
    {
        $post = PostBuilder::aPost()->build();
        $category = CategoryBuilder::aCategory()->build();

        yield 'post.id' => [$post, 'id', 'id', Type::Integer, null, false];
        yield 'post.title' => [$post, 'title', 'title', Type::String, 255, false];
        yield 'post.description' => [$post, 'description', 'description', Type::Text, null, false];
        yield 'post.content' => [$post, 'content', 'content', Type::Text, null, false];

        yield 'category.id' => [$category, 'id', 'id', Type::Integer, null, false];
        yield 'category.title' => [$category, 'title', 'title', Type::String, 255, false];
        yield 'category.description' => [$category, 'description', 'description', Type::Text, null, false];
    }
}

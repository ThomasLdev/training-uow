<?php

declare(strict_types=1);

namespace TrainingUow\Tests\ORM\Mapping\Model\Metadata;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TrainingUow\Entity\Category;
use TrainingUow\Entity\Post;
use TrainingUow\ORM\Mapping\Attributes\Column;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Attributes\PrimaryKey;
use TrainingUow\ORM\Mapping\Attributes\Table;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributeExtractor;
use TrainingUow\ORM\Mapping\Entity\Extract\Attribute\EntityAttributes;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Mapping\Model\Metadata\Exception\EntityMetadataException;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;

#[CoversClass(EntityMetadataFactory::class)]
#[CoversClass(EntityMetadata::class)]
#[CoversClass(FieldMetadata::class)]
#[CoversClass(EntityAttributeExtractor::class)]
#[CoversClass(EntityAttributes::class)]
#[CoversClass(EntityMetadataException::class)]
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
    public function it_extracts_table_name(string $className, string $expectedTableName): void
    {
        $metadata = $this->factory->fromClassName($className);

        self::assertSame($expectedTableName, $metadata->tableName);
    }

    #[Test]
    #[DataProvider('classNameProvider')]
    public function it_extracts_entity_fqcn(string $className): void
    {
        $metadata = $this->factory->fromClassName($className);

        self::assertSame($className, $metadata->entityFQCN);
    }

    #[Test]
    #[DataProvider('classNameProvider')]
    public function it_extracts_primary_key(string $className): void
    {
        $metadata = $this->factory->fromClassName($className);

        self::assertSame('id', $metadata->primaryKey);
    }

    #[Test]
    #[DataProvider('entityWithFieldsProvider')]
    public function it_extracts_field_metadata(
        string $className,
        string $propertyName,
        string $expectedColumnName,
        Type $expectedType,
        ?int $expectedLength,
        bool $expectedNullable,
    ): void {
        $metadata = $this->factory->fromClassName($className);

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
        $metadata1 = $this->factory->fromClassName(Post::class);
        $metadata2 = $this->factory->fromClassName(Post::class);

        self::assertSame($metadata1, $metadata2);
    }

    #[Test]
    public function it_returns_different_metadata_for_different_entity_classes(): void
    {
        $postMetadata = $this->factory->fromClassName(Post::class);
        $categoryMetadata = $this->factory->fromClassName(Category::class);

        self::assertNotSame($postMetadata, $categoryMetadata);
        self::assertSame('post', $postMetadata->tableName);
        self::assertSame('category', $categoryMetadata->tableName);
    }

    #[Test]
    public function it_throws_when_entity_has_no_table_attribute(): void
    {
        $entity = new class {};

        $this->expectException(EntityMetadataException::class);
        $this->expectExceptionMessage('No table name specified');

        $this->factory->fromClassName($entity::class);
    }

    #[Test]
    public function it_throws_when_entity_has_no_primary_key(): void
    {
        $entity = new #[Table('test')] class {
            #[Column(name: 'name', type: Type::String, length: 255)]
            public string $name = '';
        };

        $this->expectException(EntityMetadataException::class);
        $this->expectExceptionMessage('No primary key specified');

        $this->factory->fromClassName($entity::class);
    }

    #[Test]
    public function it_throws_when_entity_has_no_mapped_fields(): void
    {
        $entity = new #[Table('test')] class {
            #[PrimaryKey]
            public int $id = 0;
        };

        $this->expectException(EntityMetadataException::class);
        $this->expectExceptionMessage('No mapped fields found');

        $this->factory->fromClassName($entity::class);
    }

    public static function classNameProvider(): iterable
    {
        yield 'post entity' => [Post::class];
        yield 'category entity' => [Category::class];
    }

    public static function entityProvider(): iterable
    {
        yield 'post entity' => [Post::class, 'post'];
        yield 'category entity' => [Category::class, 'category'];
    }

    public static function entityWithFieldsProvider(): iterable
    {
        yield 'post.id' => [Post::class, 'id', 'id', Type::Integer, null, false];
        yield 'post.title' => [Post::class, 'title', 'title', Type::String, 255, false];
        yield 'post.description' => [Post::class, 'description', 'description', Type::Text, null, false];
        yield 'post.content' => [Post::class, 'content', 'content', Type::Text, null, false];

        yield 'category.id' => [Category::class, 'id', 'id', Type::Integer, null, false];
        yield 'category.title' => [Category::class, 'title', 'title', Type::String, 255, false];
        yield 'category.description' => [Category::class, 'description', 'description', Type::Text, null, false];
    }
}

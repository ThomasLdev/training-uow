<?php

declare(strict_types=1);

namespace TrainingUow\Tests\ORM\Mapping\Entity\Extract\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TrainingUow\ORM\Mapping\Attributes\Enum\Type;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\EntityValueExtractor;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\Exception\EntityValueExtractionException;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Mapping\Model\Metadata\FieldMetadata;
use TrainingUow\Tests\Builder\CategoryBuilder;
use TrainingUow\Tests\Builder\PostBuilder;

#[CoversClass(EntityValueExtractor::class)]
#[CoversClass(EntityValueExtractionException::class)]
final class EntityValueExtractorTest extends TestCase
{
    private EntityValueExtractor $extractor;
    private EntityMetadataFactory $metadataFactory;

    protected function setUp(): void
    {
        /** @var ContainerBuilder $container */
        $container = require __DIR__ . '/../../../../../../config/container.php';
        $this->extractor = $container->get(EntityValueExtractor::class);
        $this->metadataFactory = $container->get(EntityMetadataFactory::class);

        $reflection = new ReflectionClass(EntityMetadataFactory::class);
        $reflection->setStaticPropertyValue('cache', []);
    }

    #[Test]
    public function it_extracts_all_values_from_a_post(): void
    {
        $post = PostBuilder::aPost()
            ->withTitle('Mon titre')
            ->withDescription('Ma description')
            ->withContent('Mon contenu')
            ->build();

        $metadata = $this->metadataFactory->fromClassName($post::class);
        $snapshot = $this->extractor->extract($post, $metadata);

        self::assertNull($snapshot['id']);
        self::assertSame('Mon titre', $snapshot['title']);
        self::assertSame('Ma description', $snapshot['description']);
        self::assertSame('Mon contenu', $snapshot['content']);
    }

    #[Test]
    public function it_extracts_all_values_from_a_category(): void
    {
        $category = CategoryBuilder::aCategory()
            ->withTitle('PHP')
            ->withDescription('Articles PHP')
            ->build();

        $metadata = $this->metadataFactory->fromClassName($category::class);
        $snapshot = $this->extractor->extract($category, $metadata);

        self::assertNull($snapshot['id']);
        self::assertSame('PHP', $snapshot['title']);
        self::assertSame('Articles PHP', $snapshot['description']);
    }

    #[Test]
    public function it_returns_one_entry_per_mapped_field(): void
    {
        $post = PostBuilder::aPost()->build();
        $metadata = $this->metadataFactory->fromClassName($post::class);

        $snapshot = $this->extractor->extract($post, $metadata);

        self::assertCount(count($metadata->fieldsMetadata), $snapshot);
    }

    #[Test]
    public function it_throws_when_property_does_not_exist_on_object(): void
    {
        $post = PostBuilder::aPost()->build();

        $metadata = new EntityMetadata(
            entityFQCN: $post::class,
            tableName: 'post',
            primaryKey: 'id',
            fieldsMetadata: [
                'nonExistent' => new FieldMetadata(
                    propertyName: 'nonExistent',
                    columnName: 'non_existent',
                    type: Type::String,
                    length: 255,
                ),
            ],
        );

        $this->expectException(EntityValueExtractionException::class);
        $this->expectExceptionMessage('Could not extract value from property nonExistent');

        $this->extractor->extract($post, $metadata);
    }

    #[Test]
    public function it_extracts_default_values_when_entity_is_not_populated(): void
    {
        $post = PostBuilder::aPost()
            ->withTitle('Default post title')
            ->withDescription('Default post description')
            ->withContent('Default post content')
            ->build();

        $metadata = $this->metadataFactory->fromClassName($post::class);
        $snapshot = $this->extractor->extract($post, $metadata);

        self::assertSame('Default post title', $snapshot['title']);
        self::assertSame('Default post description', $snapshot['description']);
        self::assertSame('Default post content', $snapshot['content']);
    }
}

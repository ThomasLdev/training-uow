<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Model\Metadata;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class EntityMetadata
{
    public function __construct(
        public string $entityFQCN,
        public string $tableName,
        public string $primaryKey,
        /** @var array<string, FieldMetadata> $fieldsMetadata */
        public array $fieldsMetadata,
    ) {}
}

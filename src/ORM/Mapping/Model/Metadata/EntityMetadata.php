<?php

declare(strict_types=1);

namespace ORM\Mapping\Model\Metadata;

final readonly class EntityMetadata
{
    public function __construct(
        public string $entityFQCN,
        public string $tableName,
        public string $primaryKey,
        public array $fields,
    )
    {
    }
}
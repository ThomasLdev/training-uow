<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity\Model;

final readonly class ChangeSet
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private array $values = [],
    ) {}

    /** @return array<string, mixed> */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getColumns(): string
    {
        return implode(',', array_keys($this->values));
    }

    public function getPlaceholders(): string
    {
        return implode(', ', array_map(static fn(string $col): string => ':' . $col, array_keys($this->values)));
    }
}

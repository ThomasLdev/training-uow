<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Attributes\Enum;

use Exception;

enum Type: string
{
    case String = 'VARCHAR';
    case Integer = 'INTEGER';
    case Text = 'TEXT';
    case Float = 'FLOAT';

    /**
     * @throws Exception
     */
    public function convertPrimaryKeyToPHPValue(string $value): int|string
    {
        return match ($this) {
            self::Integer => (int)$value,
            self::String, self::Text => $value,
            self::Float => throw new Exception('The primary must be of type string or integer'),
        };
    }
}

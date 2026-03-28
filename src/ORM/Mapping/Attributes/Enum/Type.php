<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Attributes\Enum;

enum Type: string
{
    case String = 'VARCHAR';
    case Integer = 'INTEGER';
    case Text = 'TEXT';
    case Float = 'FLOAT';
}

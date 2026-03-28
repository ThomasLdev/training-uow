<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Entity\Property\Extract\Exception;

class ExtractionException extends \LogicException
{
    public static function cannotFindTableName(string $entityFQCN): self
    {
        return new self(
            'Cannot find table name for entity ' . $entityFQCN
        );
    }
}
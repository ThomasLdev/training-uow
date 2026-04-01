<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class EntityPersistenceException extends RuntimeException
{
    public static function failToGetLastIdFromSequence(string $sequence): self
    {
        return new self(sprintf('Failed to get the last inserted in sequence "%s".', $sequence));
    }

    public static function failToSetIdOnEntity(string $entityName): self
    {
        return new self(sprintf('Failed to set id on entity "%s".', $entityName));
    }

    public static function failedToGetClassReflection(string $className): self
    {
        return new self(sprintf('Failed to get reflection for class "%s".', $className));
    }

    public static function insertFailed(string $table): self
    {
        return new self(sprintf('Failed to insert into table "%s".', $table));
    }
}

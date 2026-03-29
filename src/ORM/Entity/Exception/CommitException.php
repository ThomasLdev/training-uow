<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity\Exception;

use RuntimeException;
use TrainingUow\ORM\Entity\ManagedEntity;

class CommitException extends RuntimeException
{
    public static function managedEntityCannotBePersisted(ManagedEntity $managedEntity): self
    {
        return new self(
            sprintf('Entity %s cannot be persisted.', $managedEntity->getEntity()::class)
        );
    }
}
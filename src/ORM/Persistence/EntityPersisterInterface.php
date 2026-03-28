<?php

declare(strict_types=1);

namespace ORM\Persistence;

interface EntityPersisterInterface
{
    public function insert(object $entity): void;
}
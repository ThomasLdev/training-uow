<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

interface EntityPersisterInterface
{
    public function insert(object $entity): void;
}
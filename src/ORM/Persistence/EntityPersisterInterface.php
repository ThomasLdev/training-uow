<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Persistence;

use TrainingUow\ORM\Entity\ManagedEntity;
use TrainingUow\ORM\Entity\Model\ChangeSet;

interface EntityPersisterInterface
{
    /**
     * @return string the last inserted primary key value
     */
    public function insert(ManagedEntity $managedEntity, ChangeSet $changeSet): string;

    public function update(ManagedEntity $managedEntity, ChangeSet $changeSet): void;

    public function delete(ManagedEntity $managedEntity): void;
}

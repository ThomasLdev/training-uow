<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity\Model;

use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Entity\ManagedEntity;

final readonly class ChangeSetFactory
{
    /**
     * @param array<string, mixed> $currentValues
     */
    public function get(ManagedEntity $managedEntity, array $currentValues): ChangeSet
    {
        $changeSet = [];
        $originalData = $managedEntity->getOriginalData();

        foreach ($currentValues as $propertyName => $value) {
            $field = $managedEntity->getMetadata()->fieldsMetadata[$propertyName];

            if ($managedEntity->getMetadata()->primaryKey === $propertyName) {
                continue;
            }

            if ($this->isChangeSet($managedEntity->getEntityState(), $value, $originalData[$propertyName])) {
                $changeSet[$field->columnName] = $value;
            }
        }

        return new ChangeSet($changeSet);
    }

    private function isChangeSet(EntityState $state, mixed $currentValue, mixed $originalValue): bool
    {
        if (EntityState::Managed === $state) {
            return $currentValue !== $originalValue;
        }

        return true;
    }
}

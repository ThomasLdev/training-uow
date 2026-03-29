<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity;

class ChangeSet
{
    /**
     * @param array<string, mixed> $currentValues
     * @return array<string, mixed>
     */
    public function get(ManagedEntity $managedEntity, array $currentValues): array
    {
        $changeSet = [];
        $originalData = $managedEntity->getOriginalData();

        foreach ($currentValues as $key => $value) {
            if ($value !== $originalData[$key]) {
                $changeSet[$key] = $value;
            }
        }

        return $changeSet;
    }
}

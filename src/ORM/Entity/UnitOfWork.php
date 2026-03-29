<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity;

use ReflectionClass;
use ReflectionException;
use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\EntityValueExtractor;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\EntityPersisterInterface;

use function spl_object_id;

final class UnitOfWork
{
    /** @var list<int, ManagedEntity> $managedEntities */
    private array $managedEntities = [];

    /** @var array<class-string, array<int|string, object>> $identityMapping */
    private array $identityMapping = [];

    public function __construct(
        private readonly EntityMetadataFactory $metadataFactory,
        private readonly EntityValueExtractor $entityValueExtractor,
        private readonly EntityPersisterInterface $persister
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function persist(object $entity): void
    {
        $managedEntity = $this->managedEntities[spl_object_id($entity)] ?? null;

        if (null === $managedEntity) {
            $this->createManagedEntity($entity);

            return;
        }

        if (EntityState::Removed === $managedEntity->getEntityState()) {
            $managedEntity->setEntityState(EntityState::Managed);
        }
    }

    public function remove(object $entity): void
    {
        $managedEntity = $this->managedEntities[spl_object_id($entity)] ?? null;

        if (null === $managedEntity) {
            return;
        }

        switch ($managedEntity->getEntityState()) {
            case EntityState::New: unset($this->managedEntities[spl_object_id($entity)]); break;
            case EntityState::Managed: $managedEntity->setEntityState(EntityState::Removed); break;
        }
    }

    /**
     * @throws ReflectionException
     */
    public function commit(): void
    {
        foreach ($this->managedEntities as $managedEntity) {
            switch ($managedEntity->getEntityState()) {
                case EntityState::New: $this->handleCommitNewState($managedEntity); break;
                case EntityState::Managed: $this->handleCommitManagedState($managedEntity); break;
                case EntityState::Removed: $this->handleCommitRemovedState($managedEntity); break;
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    private function createManagedEntity(object $entity): void
    {
        $metadata = $this->metadataFactory->fromClassName($entity::class);

        $this->managedEntities[spl_object_id($entity)] = new ManagedEntity(
            entity: $entity,
            state: EntityState::New,
            originalData: $this->entityValueExtractor->extract($entity, $metadata),
            metadata: $metadata,
        );
    }

    /**
     * @throws ReflectionException
     */
    private function handleCommitNewState(ManagedEntity $managedEntity): void
    {
        $primaryKeyValue = $this->persister->insert($managedEntity);
        $entity = $managedEntity->getEntity();

        $managedEntity->setEntityState(EntityState::Managed);

        new ReflectionClass($entity)
            ->getProperty($managedEntity->getMetadata()->primaryKey)
            ->setValue($entity, $primaryKeyValue)
        ;

        $managedEntity->setOriginalData(
            $this->entityValueExtractor->extract($entity, $managedEntity->getMetadata())
        );

        $this->identityMapping[$managedEntity->getEntity()::class][$primaryKeyValue] = $entity;
    }

    private function handleCommitManagedState(ManagedEntity $managedEntity): void
    {
        $changeSet = new ChangeSet()->get(
            $managedEntity,
            $this->entityValueExtractor->extract($managedEntity->getEntity(), $managedEntity->getMetadata())
        );

        if ([] === $changeSet) {
            return;
        }

        $this->persister->update($managedEntity, $changeSet);
    }

    /**
     * @throws ReflectionException
     */
    private function handleCommitRemovedState(ManagedEntity $managedEntity): void
    {
        $this->persister->delete($managedEntity);

        $entity = $managedEntity->getEntity();
        $primaryKeyValue = new ReflectionClass($entity)
            ->getProperty($managedEntity->getMetadata()->primaryKey)
            ->getValue($entity)
        ;

        unset(
            $this->managedEntities[spl_object_id($managedEntity->getEntity())],
            $this->identityMapping[$entity::class][$primaryKeyValue],
        );
    }
}
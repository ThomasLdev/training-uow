<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity;

use Exception;
use ReflectionClass;
use ReflectionException;
use TrainingUow\ORM\Entity\Enum\EntityState;
use TrainingUow\ORM\Entity\Model\ChangeSet;
use TrainingUow\ORM\Entity\Model\ChangeSetFactory;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\EntityValueExtractor;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\EntityPersisterInterface;

final class UnitOfWork
{
    /** @var array<int, ManagedEntity> */
    private array $managedEntities = [];

    /** @var array<class-string, array<int|string, object>> $identityMapping */
    private array $identityMapping = [];

    public function __construct(
        private readonly EntityMetadataFactory $metadataFactory,
        private readonly EntityValueExtractor $entityValueExtractor,
        private readonly EntityPersisterInterface $persister,
        private readonly ChangeSetFactory $changeSetFactory,
    ) {}

    /**
     * @throws ReflectionException
     */
    public function persist(object $entity): void
    {
        $managedEntity = $this->managedEntities[\spl_object_id($entity)] ?? null;

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
        $managedEntity = $this->managedEntities[\spl_object_id($entity)] ?? null;

        if (null === $managedEntity) {
            return;
        }

        switch ($managedEntity->getEntityState()) {
            case EntityState::New: unset($this->managedEntities[\spl_object_id($entity)]);
                break;
            case EntityState::Managed: $managedEntity->setEntityState(EntityState::Removed);
                break;
        }
    }

    /**
     * @throws ReflectionException
     */
    public function commit(): void
    {
        foreach ($this->managedEntities as $managedEntity) {
            switch ($managedEntity->getEntityState()) {
                case EntityState::New: $this->handleCommitNewState($managedEntity);
                    break;
                case EntityState::Managed: $this->handleCommitManagedState($managedEntity);
                    break;
                case EntityState::Removed: $this->handleCommitRemovedState($managedEntity);
                    break;
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    private function createManagedEntity(object $entity): void
    {
        $metadata = $this->metadataFactory->fromClassName($entity::class);

        $this->managedEntities[\spl_object_id($entity)] = new ManagedEntity(
            entity: $entity,
            state: EntityState::New,
            originalData: $this->entityValueExtractor->extract($entity, $metadata),
            metadata: $metadata,
        );
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function handleCommitNewState(ManagedEntity $managedEntity): void
    {
        $metadata = $managedEntity->getMetadata();
        $primaryKeyField = $metadata->fieldsMetadata[$metadata->primaryKey];

        $primaryKeyValue = $primaryKeyField->type->convertPrimaryKeyToPHPValue(
            $this->persister->insert(
                managedEntity: $managedEntity,
                changeSet: $this->computeChangeSet($managedEntity),
            ),
        );

        $entity = $managedEntity->getEntity();

        $managedEntity->setEntityState(EntityState::Managed);

        new ReflectionClass($entity)
            ->getProperty($managedEntity->getMetadata()->primaryKey)
            ->setValue($entity, $primaryKeyValue)
        ;

        $managedEntity->setOriginalData(
            $this->entityValueExtractor->extract($entity, $managedEntity->getMetadata()),
        );

        $this->identityMapping[$managedEntity->getEntity()::class][$primaryKeyValue] = $entity;
    }

    private function handleCommitManagedState(ManagedEntity $managedEntity): void
    {
        $changeSet = $this->computeChangeSet($managedEntity);

        if ([] === $changeSet->getValues()) {
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
        /** @var int|string $primaryKeyValue */
        $primaryKeyValue = new ReflectionClass($entity)
            ->getProperty($managedEntity->getMetadata()->primaryKey)
            ->getValue($entity)
        ;

        unset(
            $this->managedEntities[\spl_object_id($managedEntity->getEntity())],
            $this->identityMapping[$entity::class][$primaryKeyValue],
        );
    }

    private function computeChangeSet(ManagedEntity $managedEntity): ChangeSet
    {
        return $this->changeSetFactory->get(
            $managedEntity,
            $this->entityValueExtractor->extract($managedEntity->getEntity(), $managedEntity->getMetadata()),
        );
    }
}

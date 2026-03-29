<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity;

use ReflectionException;

class EntityManager
{
    private UnitOfWork $unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @throws ReflectionException
     */
    public function persist(object $entity): void
    {
        $this->unitOfWork->persist($entity);
    }

    /**
     * @throws ReflectionException
     */
    public function flush(): void
    {
        $this->unitOfWork->commit();
    }

    public function remove(object $entity): void
    {
        $this->unitOfWork->remove($entity);
    }

    public function find(object $entity): ?object
    {
        // TODO : implement find
        return null;
    }
}
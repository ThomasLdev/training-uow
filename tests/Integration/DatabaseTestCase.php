<?php

declare(strict_types=1);

namespace TrainingUow\Tests\Integration;

use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TrainingUow\ORM\Database\PdoWrapper;
use TrainingUow\ORM\Entity\EntityManager;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;

abstract class DatabaseTestCase extends TestCase
{
    protected EntityManager $entityManager;
    protected PDO $pdo;

    protected function setUp(): void
    {
        /** @var ContainerBuilder $container */
        $container = require __DIR__ . '/../../config/container.php';

        $reflection = new ReflectionClass(EntityMetadataFactory::class);
        $reflection->setStaticPropertyValue('cache', []);

        $this->entityManager = $container->get(EntityManager::class);
        $this->pdo = $container->get(PdoWrapper::class)->getPdo();

        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }
}

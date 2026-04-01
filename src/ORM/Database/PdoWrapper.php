<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Database;

use PDO;
use PDOException;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadata;

/**
 * Good enough for a training project, but we would use env variables and secrets otherwise ;)
 * TODO : wrap everything in a transaction
 */
final readonly class PdoWrapper
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('pgsql:host=postgres;dbname=training_uow', 'app', 'app');
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getLastInsertId(EntityMetadata $metadata): string
    {
        $lastInsertId = $this->pdo->lastInsertId(
            sprintf('%s_%s_seq', $metadata->tableName, $metadata->primaryKey),
        );

        if (false === $lastInsertId) {
            throw new PdoException('Unable to get last insert ID');
        }

        return $lastInsertId;
    }
}

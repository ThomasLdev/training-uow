<?php

declare(strict_types=1);

echo 'Starting schema import...' . PHP_EOL;
echo 'WARNING - It will reset all rows' . PHP_EOL;

$tables = json_decode(
    file_get_contents(__DIR__ . '/../schema/schema.json'),
    true,
    512,
    JSON_THROW_ON_ERROR,
);
$database = new PDO('pgsql:host=postgres;dbname=training_uow', 'app', 'app');

/**
 * @var array{
 *     table_name: string,
 *     primary_key: array<array-key, string>,
 *     columns: array<array-key, array{name: string, type: string, nullable: bool, default: mixed}>
 * } $table
 */
foreach ($tables as $table) {
    $tableName = $table['table_name'];
    $tableExist = $database
        ->query(<<<SQL
        SELECT * FROM information_schema.tables
        WHERE table_schema = 'public'
            AND table_name = '{$tableName}'
        LIMIT 1
        SQL)
        ->fetch()
    ;

    if ($tableExist) {
        echo sprintf('WARNING - Dropping table %s', $tableName) . PHP_EOL;

        $database->query("DROP TABLE IF EXISTS {$tableName}");
    }

    $columnParts = [];

    foreach ($table['columns'] as $column) {
        $nullable = !$column['nullable'] ? 'NOT NULL' : '';

        if ($column['default'] === null) {
            $default = 'NULL';
        } else {
            $default = "'" . $column['default'] . "'";
        }

        $columnParts[] = "{$column['name']} {$column['type']} {$nullable} DEFAULT {$default}";
    }

    $columnsSQL = implode(', ', $columnParts);
    $primaryKeyColumns = implode(', ', array_map(fn(string $key) => "{$key} SERIAL", $table['primary_key']));
    $primaryKeyConstraint = implode(', ', $table['primary_key']);

    $database->query(<<<SQL
        CREATE TABLE IF NOT EXISTS {$tableName} (
            {$primaryKeyColumns},
            {$columnsSQL},
            PRIMARY KEY ({$primaryKeyConstraint})
        )
    SQL);

    echo sprintf('Created table %s', $tableName) . PHP_EOL;
}

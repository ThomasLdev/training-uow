<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TrainingUow\Entity\Post;
use TrainingUow\ORM\Mapping\Model\Metadata\EntityMetadataFactory;
use TrainingUow\ORM\Persistence\EntityPersister;

$post1 = new Post()
    ->setContent('this is a new post 1 !')
    ->setTitle('new post 1')
    ->setDescription('new post description 1')
;

$post2 = new Post()
    ->setContent('this is a new post 2 !')
    ->setTitle('new post 2')
    ->setDescription('new post description 2')
;

$database = new PDO('pgsql:host=postgres;dbname=training_uow', 'app', 'app');

$persister = new EntityPersister($database, new EntityMetadataFactory());
$persister->bulkInsert([$post1, $post2]);

$persistedPost = $database->query("SELECT * FROM post WHERE title LIKE '%new post'");
var_dump($persistedPost->fetchAll());
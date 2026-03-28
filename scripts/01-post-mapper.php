<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TrainingUow\Entity\Post;
use TrainingUow\ORM\Persistence\EntityPersister;

$post = new Post()
    ->setContent('this is a new post !')
    ->setTitle('new post')
    ->setDescription('new post description')
;

$database = new PDO('pgsql:host=postgres;dbname=training_uow', 'app', 'app');

$persister = new EntityPersister($database);
$persister->insert($post);

$persistedPost = $database->query("SELECT * FROM post WHERE title = 'new post'");
var_dump($persistedPost->fetchAll());
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TrainingUow\Entity\Category;
use TrainingUow\Entity\Post;
use TrainingUow\ORM\Entity\UnitOfWork;
use TrainingUow\ORM\Mapping\Entity\Extract\Value\EntityValueExtractor;
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

$category1 = new Category()
    ->setTitle('category 1')
    ->setDescription('category description 1')
;

$category2 = new Category()
    ->setTitle('category 2')
    ->setDescription('category description 2')
;

//$database = new PDO('pgsql:host=postgres;dbname=training_uow', 'app', 'app');

$unitOfWork = new UnitOfWork(new EntityMetadataFactory(), new EntityValueExtractor(), new EntityPersister());
//$persister->bulkInsert([$post1, $post2, $category1, $category2]);
$unitOfWork->persist($post1);

//$persistedPost = $database->query("SELECT * FROM post WHERE title LIKE '%new post'");
//var_dump($persistedPost->fetchAll());

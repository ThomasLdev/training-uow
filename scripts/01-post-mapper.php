<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use TrainingUow\Entity\Category;
use TrainingUow\Entity\Post;
use TrainingUow\ORM\Entity\EntityManager;

/** @var ContainerBuilder $container */
$container = require __DIR__ . '/../config/container.php';

$entityManager = $container->get(EntityManager::class);

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

$entityManager->persist($post1);
$entityManager->persist($post2);
$entityManager->persist($category1);
$entityManager->persist($category2);

$entityManager->flush();

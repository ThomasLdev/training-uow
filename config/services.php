<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TrainingUow\ORM\Entity\EntityManager;
use TrainingUow\ORM\Persistence\EntityPersister;
use TrainingUow\ORM\Persistence\EntityPersisterInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('TrainingUow\\', '../src/')
        ->exclude('../src/Entity/')
        ->public()
    ;

    $services->alias(EntityPersisterInterface::class, EntityPersister::class);
};

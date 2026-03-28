<?php

declare(strict_types=1);

namespace ORM\Mapping\Model\Metadata;

use Entity\Post;

class EntityMetadataFactory
{
    public function __construct(private object $entity)
    {
    }

    public function createFromEntity(): EntityMetadata
    {
        $fakeData = new Post();
        $fakeData->content = 'this is a new post !';
        $fakeData->title = 'new post';
        $fakeData->description = 'new post';

        // TODO : use refelction to inspect object attributes and create the metadata

        return new EntityMetadata('', '', '', []);
    }
}
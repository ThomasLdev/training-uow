<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Mapping\Attributes;

use Attribute;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey {}

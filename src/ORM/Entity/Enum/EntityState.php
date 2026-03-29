<?php

declare(strict_types=1);

namespace TrainingUow\ORM\Entity\Enum;

enum EntityState: string
{
    case New = "NEW";
    case Managed = "MANAGED";
    case Removed = "REMOVED";
}
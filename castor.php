<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(description: 'Run PHPUnit tests')]
function tests(): void
{
    io()->title('Running PHPUnit');
    run('vendor/bin/phpunit --testdox');
}

#[AsTask(description: 'Run PHPStan static analysis')]
function phpstan(): void
{
    io()->title('Running PHPStan');
    run('vendor/bin/phpstan analyse');
}

#[AsTask(name: 'cs-fix', description: 'Fix code style with PHP CS Fixer')]
function csFix(): void
{
    io()->title('Running PHP CS Fixer');
    run('vendor/bin/php-cs-fixer fix');
}

#[AsTask(name: 'cs-check', description: 'Check code style with PHP CS Fixer')]
function csCheck(): void
{
    io()->title('Checking PHP CS Fixer');
    run('vendor/bin/php-cs-fixer fix --dry-run');
}

#[AsTask(name: 'cs-check-fix', description: 'Check code style with PHP CS Fixer with Fix')]
function csCheckFix(): void
{
    io()->title('Checking PHP CS Fixer');
    run('vendor/bin/php-cs-fixer fix');
}

#[AsTask(description: 'Run Rector dry-run')]
function rector(): void
{
    io()->title('Running Rector without applying changes');
    run('vendor/bin/rector --dry-run');
}

#[AsTask(description: 'Run Rector')]
function rectorFix(): void
{
    io()->title('Running Rector');
    run('vendor/bin/rector');
}

#[AsTask(description: 'Run all quality checks')]
function ci(): void
{
    tests();
    phpstan();
    rector();
    csCheck();
}

#[AsTask(description: 'Run all quality checks with fixes')]
function ciFix(): void
{
    phpstan();
    rectorFix();
    csCheckFix();
    tests();
}

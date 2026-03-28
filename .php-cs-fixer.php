<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/scripts',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,

        // Imports
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],

        // Spacing & alignment
        'single_line_empty_body' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'use',
                'curly_brace_block',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'return',
            ],
        ],
        'no_whitespace_in_blank_line' => true,
        'concat_space' => ['spacing' => 'one'],
        'type_declaration_spaces' => true,

        // Clean code
        'no_alias_functions' => true,
        'cast_spaces' => ['space' => 'none'],
        'no_empty_statement' => true,
        'no_unneeded_braces' => true,
        'no_trailing_comma_in_singleline' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'match', 'parameters']],

        // Strict
        'declare_strict_types' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);

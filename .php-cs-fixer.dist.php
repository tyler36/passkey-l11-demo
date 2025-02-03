<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->exclude([
        'bootstrap/cache',
        'storage',
        'vendor'
    ]);

return (new Config())
    ->setRules([
        // @see https://mlocati.github.io/php-cs-fixer-configurator
        '@PER-CS' => true,
        '@PhpCsFixer' => true,
        '@PHP83Migration' => true,
        // Overrides
        'no_short_bool_cast' => true,
        'simplified_null_return' => true,
        // Risky, these rules require setRiskyAllowed(true)
        '@PhpCsFixer:risky' => true,
        'date_time_create_from_format_call' => true,
        'mb_str_functions' => true,
        // Laravel
        'braces_position' => [
            'classes_opening_brace'             => 'next_line_unless_newline_at_signature_end',
            'anonymous_classes_opening_brace'   => 'next_line_unless_newline_at_signature_end',
            'anonymous_functions_opening_brace' => 'same_line',
        ],
        'final_internal_class' => false,
        'global_namespace_import' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_empty_comment' => false,
        'no_extra_blank_lines' => false,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'phpdoc_to_return_type' => true,
        'single_trait_insert_per_statement' => false,
        'static_lambda' => false,
        'yoda_style' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);

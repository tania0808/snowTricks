<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
                'const' => 'one',
                'trait_import' => 'one',
            ],
        ],

        '@PSR2' => true,
        'braces' => [
            'allow_single_line_closure' => false,
            'position_after_functions_and_oop_constructs' => 'same',
        ],
        'phpdoc_to_comment' => false,
        'cast_spaces' => [
            'space' => 'none',
        ],
        'linebreak_after_opening_tag' => true,
        'no_whitespace_in_blank_line' => true,

        'ordered_imports' => true,
        'single_quote' => true,
        'whitespace_after_comma_in_array' => true,
        'trailing_comma_in_multiline' => true,

        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,


        'indentation_type' => true,

        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],
    ])
    ->setFinder($finder)
;
